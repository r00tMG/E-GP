# app/api/routes/prediction.py
from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from app.databases.database import get_db
from datetime import datetime, timedelta
from sqlalchemy import func
from app.models import models
import joblib
import pandas as pd
from pathlib import Path

from app.security.oauth2 import get_current_user

router = APIRouter()



MODEL_PATH = Path("./mls/models/RandomForest_pipeline.pkl")

pipeline = joblib.load(MODEL_PATH)


def predict_next_day(features: dict) -> float:
    X = pd.DataFrame([features])
    prediction = pipeline.predict(X)[0]
    print("prediction random forest :", pipeline.predict(X))
    return max(prediction, 0)

@router.get("/annonces/{annonce_id}/prediction")
def predict_kilos_next_day(
    annonce_id: int,
    db: Session = Depends(get_db),
    current_user=Depends(get_current_user)
):
    # Vérifier que l'annonce appartient au GP
    annonce = (
        db.query(models.Annonce)
        .filter(
            models.Annonce.id == annonce_id,
            models.Annonce.gp_id == current_user.id
        )
        .first()
    )

    if not annonce:
        raise HTTPException(status_code=404, detail="Annonce introuvable")

    snapshot = datetime.utcnow()

    features = compute_features(db, annonce, snapshot)
    prediction = predict_next_day(features)

    return {
        "annonce_id": annonce.id,
        "date_prediction": snapshot,
        "kilos_predits_jour_suivant": round(prediction, 2),
        "kilos_restants": features["kilos_remaining"],
        "days_until_departure": features["days_until_departure"]
    }


def compute_features(db, annonce, snapshot: datetime):

    reserved_before = (
        db.query(func.coalesce(func.sum(models.ReservationItem.weight), 0))
        .join(models.Reservation)
        .filter(
            models.Reservation.annonce_id == annonce.id,
            models.Reservation.created_at < snapshot,
            models.Reservation.status == "CONFIRMED"
        )
        .scalar()
    )

    kilos_total = annonce.kilos_disponibles or 0
    kilos_remaining = max(kilos_total - reserved_before, 0)

    hist_7d = (
        db.query(func.coalesce(func.sum(models.ReservationItem.weight), 0))
        .join(models.Reservation)
        .filter(
            models.Reservation.annonce_id == annonce.id,
            models.Reservation.created_at >= snapshot - timedelta(days=7),
            models.Reservation.created_at < snapshot,
            models.Reservation.status == "CONFIRMED"
        )
        .scalar()
    )

    hist_30d = (
        db.query(func.coalesce(func.sum(models.ReservationItem.weight), 0))
        .join(models.Reservation)
        .filter(
            models.Reservation.annonce_id == annonce.id,
            models.Reservation.created_at >= snapshot - timedelta(days=30),
            models.Reservation.created_at < snapshot,
            models.Reservation.status == "CONFIRMED"
        )
        .scalar()
    )

    route_30d = (
        db.query(func.count(models.Reservation.id))
        .join(models.Annonce)
        .filter(
            models.Annonce.origin == annonce.origin,
            models.Annonce.destination == annonce.destination,
            models.Reservation.created_at >= snapshot - timedelta(days=30),
            models.Reservation.status == "CONFIRMED"
        )
        .scalar()
    )
    occupancy_rate = (
        reserved_before / kilos_total
        if kilos_total > 0 else 0
    )
    print("Reserved before:", reserved_before)
    print("Occupancy rate:", occupancy_rate)

    return {
        "annonce_id": annonce.id,
        "gp_id": annonce.gp_id,
        "date_snapshot": snapshot,
        "date_depart": annonce.date_depart,
        "origin": annonce.origin,
        "destination": annonce.destination,

        "kilos_total": kilos_total,
        "kilos_reserved_before": reserved_before,
        "kilos_remaining": kilos_remaining,
        "occupancy_rate": occupancy_rate,

        "days_until_departure": (annonce.date_depart - snapshot).days,
        "dow_snapshot": snapshot.weekday(),
        "is_weekend": snapshot.weekday() >= 5,
        "month_snapshot": snapshot.month,

        "hist_bookings_7d": hist_7d,
        "hist_bookings_30d": hist_30d,
        "route_bookings_30d": route_30d,

    }

