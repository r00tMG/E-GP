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
# Webhook Stripe : confirmation paiement
# @router.post("/stripe/webhook")
# async def stripe_webhook(request: Request, db: Session = Depends(get_db)):
#     payload = await request.body()
#     sig_header = request.headers.get("stripe-signature")
#     endpoint_secret = os.getenv("STRIPE_WEBHOOK_SECRET")
#
#     try:
#         event = stripe.Webhook.construct_event(payload, sig_header, endpoint_secret)
#     except stripe.error.SignatureVerificationError:
#         return JSONResponse(status_code=400, content={"error": "Invalid signature"})
#
#     # Checkout session completed
#     if event["type"] == "checkout.session.completed":
#         session = event["data"]["object"]
#         session_id = session["id"]
#
#         # 1 Récupération du paiement correspondant
#         payment = db.query(models.Payment).filter(models.Payment.stripe_session_id == session_id).first()
#         if not payment:
#             return JSONResponse(status_code=404, content={"error": "Payment not found"})
#
#         # 2 Mise à jour du statut du paiement
#         payment.status = models.PaymentStatus.SUCCEEDED
#
#         # 3 Mise à jour du statut de la réservation
#         reservation = db.query(models.Reservation).filter(models.Reservation.id == payment.reservation_id).first()
#         if reservation:
#             reservation.status = "CONFIRMED"
#             db.commit()
#             db.refresh(reservation)
#             # Génération de la facture PDF
#             pdf_path = generate_invoice_pdf(reservation, payment)
#             print(f"Facture générée : {pdf_path}")
#             # Envoi email au client
#             if reservation.user.email:
#                 send_invoice_email(reservation.user.email, pdf_path, subject="Votre facture de réservation")
#
#             # Envoi email au GP
#             gp_email = reservation.annonce.gp.email
#             if gp_email:
#                 send_invoice_email(gp_email, pdf_path, subject="Nouvelle réservation - facture")
#
#         db.commit()
#         print(f" Paiement confirmé et réservation confirmée pour session {session_id}")
#
#     #  Gérer d'autres types d'événements si nécessaire
#     elif event["type"] == "payment_intent.payment_failed":
#         intent = event["data"]["object"]
#         session_id = intent.get("metadata", {}).get("stripe_session_id")
#         payment = db.query(models.Payment).filter(models.Payment.stripe_session_id == session_id).first()
#         if payment:
#             payment.status = "failed"
#             reservation = db.query(models.Reservation).with_for_update().filter(
#                 models.Reservation.id == int(payment.reservation_id)).first()
#             if reservation and reservation.status == models.StatusReservation.PENDING:
#                 # restaurer les kilos dans l'annonce
#                 annonce = db.query(models.Annonce).with_for_update().filter(
#                     models.Annonce.id == reservation.annonce_id).first()
#                 if annonce:
#                     annonce.kilos_disponibles = (
#                                 float(annonce.kilos_disponibles or 0) + float(reservation.kilos_reserved or 0))
#                     db.add(annonce)
#                 # annuler la réservation
#                 reservation.status = models.StatusReservation.CANCELLED
#                 db.add(reservation)
#             db.commit()
#             print(f" Paiement échoué pour session {session_id}")
#
#     return JSONResponse(status_code=200, content={"received": True})
@router.post("/stripe/webhook")
async def stripe_webhook(request: Request, db: Session = Depends(get_db)):
    print("test webhook 1")
    payload = await request.body()
    sig_header = request.headers.get("stripe-signature")
    endpoint_secret = os.getenv("STRIPE_WEBHOOK_SECRET")
    print("Endpoint Secret: ",endpoint_secret)

    try:
        print("test webhook 2")
        event = stripe.Webhook.construct_event(payload, sig_header, endpoint_secret)
    except stripe.error.SignatureVerificationError:
        print("test webhook 2_")
        return JSONResponse(status_code=400, content={"error": "Invalid signature"})

    try:
        print("test webhook 3", event["type"])
        if event["type"] == "checkout.session.completed":
            print("test webhook 4")
            session = event["data"]["object"]
            session_id = session["id"]

            payment = db.query(models.Payment).filter(
                models.Payment.stripe_session_id == session_id
            ).first()

            if not payment:
                print("test webhook 5")
                return JSONResponse(status_code=200, content={"received": True})

            # Idempotence
            if payment.status == models.PaymentStatus.SUCCEEDED:
                print("test webhook 6")
                return JSONResponse(status_code=200, content={"received": True})

            payment.status = models.PaymentStatus.SUCCEEDED

            reservation = db.query(models.Reservation).filter(
                models.Reservation.id == payment.reservation_id
            ).first()

            if reservation:
                print("test webhook 7")
                reservation.status = models.StatusReservation.CONFIRMED

            db.commit()

            # Side effects après commit
            pdf_path = generate_invoice_pdf(reservation, payment)

            if reservation.user.email:
                send_invoice_email(
                    reservation.user.email,
                    pdf_path,
                    subject="Votre facture de réservation"
                )

            gp_email = reservation.annonce.gp.email
            print("test webhook 8")
            if gp_email:
                print("test webhook 9")
                send_invoice_email(
                    gp_email,
                    pdf_path,
                    subject="Nouvelle réservation - facture"
                )

        elif event["type"] == "payment_intent.payment_failed":
            print("test webhook 10")
            intent = event["data"]["object"]
            session_id = intent.get("metadata", {}).get("stripe_session_id")

            payment = db.query(models.Payment).filter(
                models.Payment.stripe_session_id == session_id
            ).first()

            if payment:
                print("test webhook 11")
                payment.status = models.PaymentStatus.FAILED

                reservation = db.query(models.Reservation).filter(
                    models.Reservation.id == payment.reservation_id
                ).first()

                if reservation and reservation.status == models.StatusReservation.PENDING:
                    print("test webhook 12")
                    annonce = db.query(models.Annonce).filter(
                        models.Annonce.id == reservation.annonce_id
                    ).first()

                    if annonce:
                        print("test webhook 13")
                        annonce.kilos_disponibles += reservation.kilos_reserved or 0

                    reservation.status = models.StatusReservation.CANCELLED

                db.commit()

    except Exception as e:
        print("test webhook 14")
        db.rollback()
        print("WEBHOOK ERROR:", e)

    # Toujours 200 pour Stripe
    return JSONResponse(status_code=200, content={"received": True})
