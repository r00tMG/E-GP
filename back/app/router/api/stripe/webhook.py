import os

import stripe
from fastapi import APIRouter, Depends, Request
from fastapi.responses import JSONResponse
from sqlalchemy.orm import Session

from app.databases.database import get_db
from app.models import models
from app.router.api.stripe.utils.email import send_invoice_email
from app.router.api.stripe.utils.invoice import generate_invoice_pdf

router=APIRouter(
    tags=['Stripe']
)
# 🔹 Webhook Stripe : confirmation paiement
@router.post("/stripe/webhook")
async def stripe_webhook(request: Request, db: Session = Depends(get_db)):
    payload = await request.body()
    sig_header = request.headers.get("stripe-signature")
    endpoint_secret = os.getenv("STRIPE_WEBHOOK_SECRET")

    try:
        event = stripe.Webhook.construct_event(payload, sig_header, endpoint_secret)
    except stripe.error.SignatureVerificationError:
        return JSONResponse(status_code=400, content={"error": "Invalid signature"})

    # Checkout session completed
    if event["type"] == "checkout.session.completed":
        session = event["data"]["object"]
        session_id = session["id"]

        # 1 Récupération du paiement correspondant
        payment = db.query(models.Payment).filter(models.Payment.stripe_session_id == session_id).first()
        if not payment:
            return JSONResponse(status_code=404, content={"error": "Payment not found"})

        # 2 Mise à jour du statut du paiement
        payment.status = "paid"

        # 3 Mise à jour du statut de la réservation
        reservation = db.query(models.Reservation).filter(models.Reservation.id == payment.reservation_id).first()
        if reservation:
            reservation.status = "confirmed"
            db.commit()
            db.refresh(reservation)
            # Génération de la facture PDF
            pdf_path = generate_invoice_pdf(reservation, payment)
            print(f"📄 Facture générée : {pdf_path}")
            # Envoi email au client
            if reservation.user.email:
                send_invoice_email(reservation.user.email, pdf_path, subject="Votre facture de réservation")

            # Envoi email au GP
            gp_email = reservation.annonce.gp.email
            if gp_email:
                send_invoice_email(gp_email, pdf_path, subject="Nouvelle réservation - facture")

        db.commit()
        print(f" Paiement confirmé et réservation confirmée pour session {session_id}")

    #  Gérer d'autres types d'événements si nécessaire
    elif event["type"] == "payment_intent.payment_failed":
        intent = event["data"]["object"]
        session_id = intent.get("metadata", {}).get("stripe_session_id")
        payment = db.query(models.Payment).filter(models.Payment.stripe_session_id == session_id).first()
        if payment:
            payment.status = "failed"
            reservation = db.query(models.Reservation).with_for_update().filter(
                models.Reservation.id == int(payment.reservation_id)).first()
            if reservation and reservation.status == models.StatusReservation.PENDING:
                # restaurer les kilos dans l'annonce
                annonce = db.query(models.Annonce).with_for_update().filter(
                    models.Annonce.id == reservation.annonce_id).first()
                if annonce:
                    annonce.kilos_disponibles = (
                                float(annonce.kilos_disponibles or 0) + float(reservation.kilos_reserved or 0))
                    db.add(annonce)
                # annuler la réservation
                reservation.status = models.StatusReservation.CANCELLED
                db.add(reservation)
            db.commit()
            print(f" Paiement échoué pour session {session_id}")

    return JSONResponse(status_code=200, content={"received": True})