import os
import stripe
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session, joinedload
from app.databases.database import get_db
from app.models import models
from app.models.models import Payment
from app.schemas.schemas_payment_create import PaymentRequest

stripe.api_key = os.getenv("STRIPE_SECRET_KEY")
router=APIRouter(
    tags=['Stripe']
)

@router.post("/create-session")
@router.post("/stripe/create-checkout-session")
async def create_checkout_session(data: PaymentRequest, db: Session = Depends(get_db)):
    try:
        # 1 Récupération de la réservation complète avec relations
        reservation = (
            db.query(models.Reservation)
            .options(
                joinedload(models.Reservation.annonce).joinedload(models.Annonce.gp),
                joinedload(models.Reservation.user),
                joinedload(models.Reservation.items),
                joinedload(models.Reservation.special_items),
            )
            .filter(models.Reservation.id == data.reservation_id)
            .first()
        )

        if not reservation:
            raise HTTPException(status_code=404, detail="Réservation introuvable")

        # 2 Calcul du montant total et de la commission
        total_amount = float(reservation.total_price)
        commission_rate = 0.15
        commission_amount = round(total_amount * commission_rate, 2)

        gp_stripe_account = reservation.annonce.gp.stripe_account_id
        if not gp_stripe_account:
            raise HTTPException(
                status_code=400,
                detail="Le GP n’a pas encore de compte Stripe connecté",
            )

        # 3 Préparation des lignes d’articles pour la facture Stripe
        line_items = []

        # Items au kilo
        for item in reservation.items:
            line_items.append(
                {
                    "price_data": {
                        "currency": "eur",
                        "unit_amount": int(float(item.price_per_kg) * 100),
                        "product_data": {
                            "name": f"{item.item_name} ({item.weight} kg)",
                        },
                    },
                    "quantity": 1,
                }
            )

        # Items à la pièce
        for s_item in reservation.special_items:
            line_items.append(
                {
                    "price_data": {
                        "currency": "eur",
                        "unit_amount": int(float(s_item.price_per_piece) * 100),
                        "product_data": {
                            "name": f"{s_item.item_name} (x{s_item.quantity})",
                        },
                    },
                    "quantity": 1,
                }
            )

        # Si aucun item détaillé, fallback avec total
        if not line_items:
            line_items = [
                {
                    "price_data": {
                        "currency": "eur",
                        "unit_amount": int(total_amount * 100),
                        "product_data": {"name": f"Réservation #{reservation.id}"},
                    },
                    "quantity": 1,
                }
            ]

        # 4 Création de la session Checkout Stripe
        session = stripe.checkout.Session.create(
            payment_method_types=["card"],
            mode="payment",
            line_items=line_items,
            payment_intent_data={
                "application_fee_amount": int(commission_amount * 100),
                "transfer_data": {
                    "destination": gp_stripe_account,
                },
                "metadata": {
                    "reservation_id": str(reservation.id),
                    "gp_id": str(reservation.annonce.gp.id),
                    "client_id": str(reservation.user.id),
                },
            },
            success_url=f"{os.getenv('FRONTEND_URL')}/payment-success?session_id={{CHECKOUT_SESSION_ID}}",
            cancel_url=f"{os.getenv('FRONTEND_URL')}/payment-cancel",
        )

        # 5 Enregistrement du paiement
        payment = Payment(
            reservation_id=reservation.id,
            amount=total_amount,
            commission=commission_amount,
            status="pending",
            stripe_session_id=session.id,
        )

        db.add(payment)
        db.commit()
        db.refresh(payment)

        return {"checkout_url": session.url}

    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

