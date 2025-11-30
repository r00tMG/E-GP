import os
import stripe
from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session, joinedload
from app.databases.database import get_db
from app.models import models
from app.models.models import Payment
from app.schemas.schemas_payment_create import PaymentRequest
from app.security.oauth2 import get_current_user

stripe.api_key = os.getenv("STRIPE_SECRET_KEY")
router=APIRouter(
    tags=['Stripe']
)

#@router.post("/create-session")

# @router.post("/stripe/create-checkout-session")
# async def create_checkout_session(data: PaymentRequest, db: Session = Depends(get_db), current_user=Depends(get_current_user)):
#     print("test test")
#     try:
#         # 1 Récupération de la réservation complète avec relations
#         reservation = (
#             db.query(models.Reservation)
#             .options(
#                 joinedload(models.Reservation.annonce).joinedload(models.Annonce.gp),
#                 joinedload(models.Reservation.user),
#                 joinedload(models.Reservation.items),
#                 joinedload(models.Reservation.special_items),
#             )
#             .filter(models.Reservation.id == data.reservation_id)
#             .first()
#         )
#         print("details reservation: ", reservation.id)
#         if not reservation:
#             raise HTTPException(status_code=404, detail="Réservation introuvable")
#
#             # 2) sécurité : seul le client propriétaire peut initier le paiement
#         if reservation.user_id != current_user.id:
#             raise HTTPException(status_code=403, detail="Non autorisé à payer cette réservation")
#
#             # 3) statut
#         if reservation.status != models.StatusReservation.PENDING:
#             raise HTTPException(status_code=400, detail=f"Réservation dans un statut invalide: {reservation.status}")
#
#         if not reservation:
#             raise HTTPException(status_code=404, detail="Réservation introuvable")
#
#         # 2 Calcul du montant total et de la commission
#         total_amount = float(reservation.total_price)
#         commission_rate = 0.15
#         commission_amount = round(total_amount * commission_rate, 2)
#
#         gp_stripe_account = reservation.annonce.gp.stripe_account_id
#         if not gp_stripe_account:
#             raise HTTPException(
#                 status_code=400,
#                 detail="Le GP n’a pas encore de compte Stripe connecté",
#             )
#
#         # 3 Préparation des lignes d’articles pour la facture Stripe
#         line_items = []
#
#         # Items au kilo
#         for item in reservation.items:
#             line_items.append(
#                 {
#                     "price_data": {
#                         "currency": "eur",
#                         "unit_amount": int(float(item.price_per_kg) * 100),
#                         "product_data": {
#                             "name": f"{item.item_name} ({item.weight} kg)",
#                         },
#                     },
#                     "quantity": 1,
#                 }
#             )
#
#         # Items à la pièce
#         for s_item in reservation.special_items:
#             line_items.append(
#                 {
#                     "price_data": {
#                         "currency": "eur",
#                         "unit_amount": int(float(s_item.price_per_piece) * 100),
#                         "product_data": {
#                             "name": f"{s_item.item_name} (x{s_item.quantity})",
#                         },
#                     },
#                     "quantity": 1,
#                 }
#             )
#
#         # Si aucun item détaillé, fallback avec total
#         if not line_items:
#             line_items = [
#                 {
#                     "price_data": {
#                         "currency": "eur",
#                         "unit_amount": int(total_amount * 100),
#                         "product_data": {"name": f"Réservation #{reservation.id}"},
#                     },
#                     "quantity": 1,
#                 }
#             ]
#
#         # 4 Création de la session Checkout Stripe
#         # session = stripe.checkout.Session.create(
#         #     payment_method_types=["card"],
#         #     mode="payment",
#         #     line_items=line_items,
#         #     payment_intent_data={
#         #         "application_fee_amount": int(commission_amount * 100),
#         #         "transfer_data": {
#         #             "destination": gp_stripe_account,
#         #         },
#         #         "metadata": {
#         #             "reservation_id": str(reservation.id),
#         #             "gp_id": str(reservation.annonce.gp.id),
#         #             "client_id": str(reservation.user.id),
#         #         },
#         #     },
#         #     success_url=f"{os.getenv('FRONTEND_URL')}/payment-success?session_id={{CHECKOUT_SESSION_ID}}",
#         #     cancel_url=f"{os.getenv('FRONTEND_URL')}/payment-cancel",
#         # )
#
#         # ... après la préparation des line_items et commission_amount ...
#
#         metadata = {
#             "reservation_id": str(reservation.id),
#             "gp_id": str(reservation.annonce.gp.id),
#             "client_id": str(reservation.user.id),
#         }
#
#         session_kwargs = {
#             "payment_method_types": ["card"],
#             "mode": "payment",
#             "line_items": line_items,
#             "success_url": f"{os.getenv('FRONTEND_URL')}/payment-success?reservation_id={reservation.id}&session_id={{CHECKOUT_SESSION_ID}}",
#             "cancel_url": f"{os.getenv('FRONTEND_URL')}/payment-cancel?reservation_id={reservation.id}",
#         }
#
#         gp_stripe_account = getattr(reservation.annonce.gp, "stripe_account_id", None)
#
#         if gp_stripe_account:
#             session_kwargs["payment_intent_data"] = {
#                 "application_fee_amount": int(commission_amount * 100),
#                 "transfer_data": {"destination": gp_stripe_account},
#                 "metadata": metadata,
#             }
#         else:
#             session_kwargs["payment_intent_data"] = {
#                 "metadata": metadata
#             }
#
#         session = stripe.checkout.Session.create(**session_kwargs)
#
#         payment = Payment(
#             reservation_id=reservation.id,
#             amount=total_amount,
#             commission=commission_amount,
#             status="pending",
#             stripe_session_id=session.id,
#             receiver_stripe_account=gp_stripe_account  # facultatif, utile pour la suite
#         )
#
#         db.add(payment)
#         db.commit()
#         db.refresh(payment)
#
#         return {"checkout_url": session.url, "payment_id": payment.id}
#
#     except Exception as e:
#         raise HTTPException(status_code=400, detail=str(e))

@router.post("/stripe/create-checkout-session")
async def create_checkout_session(
    data: PaymentRequest,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user),
):
    print("test test")
    try:
        # 1) récupérer la réservation
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

        # sécurité : seul le client propriétaire peut initier le paiement
        if reservation.user_id != current_user.id:
            raise HTTPException(status_code=403, detail="Non autorisé à payer cette réservation")

        # statut attendu
        if reservation.status != models.StatusReservation.PENDING:
            raise HTTPException(status_code=400, detail=f"Réservation dans un statut invalide: {reservation.status}")

        # 2) réutiliser une session pending existante si possible
        existing_payment = (
            db.query(Payment)
            .filter(Payment.reservation_id == reservation.id, Payment.status == "pending")
            .first()
        )
        if existing_payment and existing_payment.stripe_session_id:
            # Optionnel: vérifier la session via Stripe si besoin
            return {
                "checkout_url": f"https://checkout.stripe.com/pay/{existing_payment.stripe_session_id}",
                "payment_id": existing_payment.id,
                "reservation_id": reservation.id,
            }

        # 3) calcul amount / commission côté serveur
        total_amount = float(reservation.total_price or 0)
        if total_amount <= 0:
            raise HTTPException(status_code=400, detail="Montant invalide pour la réservation")

        commission_rate = 0.15
        commission_amount = round(total_amount * commission_rate, 2)

        # 4) préparer line_items
        line_items = []
        for item in reservation.items:
            line_items.append({
                "price_data": {
                    "currency": "eur",
                    "unit_amount": int(float(item.price_per_kg) * 100),
                    "product_data": {"name": f"{item.item_name} ({item.weight} kg)"},
                },
                "quantity": 1,
            })
        for s_item in reservation.special_items:
            line_items.append({
                "price_data": {
                    "currency": "eur",
                    "unit_amount": int(float(s_item.price_per_piece) * 100),
                    "product_data": {"name": f"{s_item.item_name} (x{s_item.quantity})"},
                },
                "quantity": 1,
            })
        if not line_items:
            line_items = [{
                "price_data": {
                    "currency": "xof",
                    "unit_amount": int(total_amount * 100),
                    "product_data": {"name": f"Réservation #{reservation.id}"},
                },
                "quantity": 1,
            }]

        # 5) création session Stripe (gère connect optionnel)
        metadata = {
            "reservation_id": str(reservation.id),
            "gp_id": str(reservation.annonce.gp.id) if reservation.annonce and reservation.annonce.gp else "",
            "client_id": str(reservation.user.id),
        }
        print("metadata: ", metadata)

        session_kwargs = {
            "payment_method_types": ["card"],
            "mode": "payment",
            "line_items": line_items,
            "success_url": f"{os.getenv('FRONTEND_URL')}/payment-success?reservation_id={reservation.id}&session_id={{CHECKOUT_SESSION_ID}}",
            "cancel_url": f"{os.getenv('FRONTEND_URL')}/payment-cancel?reservation_id={reservation.id}",
        }


        gp_stripe_account = getattr(reservation.annonce.gp, "stripe_account_id", None) if reservation.annonce and reservation.annonce.gp else None

        if gp_stripe_account:
            # si tu utilises Connect plus tard -> split payment
            session_kwargs["payment_intent_data"] = {
                "application_fee_amount": int(commission_amount * 100),
                "transfer_data": {"destination": gp_stripe_account},
                "metadata": metadata,
            }
            print("session kwargs si true: ", session_kwargs)
        else:
            # pas de connect -> tout sur ton compte plateforme, on met juste metadata
            session_kwargs["payment_intent_data"] = {"metadata": metadata}
            print("session kwargs si false: ", session_kwargs)

        session = stripe.checkout.Session.create(**session_kwargs)

        # 6) enregistrer le Payment en DB
        payment = Payment(
            reservation_id=reservation.id,
            amount=total_amount,
            commission=commission_amount,
            status="pending",
            stripe_session_id=session.id,
            #receiver_stripe_account=gp_stripe_account if gp_stripe_account else None
        )
        print("payment: ", payment)

        db.add(payment)
        db.commit()
        db.refresh(payment)

        return {"checkout_url": session.url, "payment_id": payment.id, "reservation_id": reservation.id}

    except HTTPException:
        raise
    except Exception as e:
        # log proprement en dev
        raise HTTPException(status_code=400, detail=str(e))

