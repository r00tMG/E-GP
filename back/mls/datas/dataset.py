from datetime import datetime, timedelta
import pandas as pd
from sqlalchemy.orm import Session
from app.databases.database import SessionLocal
from sqlalchemy import func
from app.models import models


def generate_snapshots(start_date, end_date):
    current = start_date
    while current <= end_date:
        yield current
        current += timedelta(days=1)

def build_ml_dataset(db: Session):
    rows = []
    today = datetime.utcnow()

    annonces = db.query(models.Annonce).all()

    for annonce in annonces:
        # snapshots possibles
        snapshot_end = min(annonce.date_depart - timedelta(days=1), today - timedelta(days=7))
        snapshot_start = annonce.date_depart - timedelta(days=60)

        if snapshot_start >= snapshot_end:
            continue

        for snapshot in generate_snapshots(snapshot_start, snapshot_end):

            # 1️⃣ kilos réservés AVANT snapshot
            reserved_before = (
                db.query(func.coalesce(func.sum(models.ReservationItem.weight), 0))
                .join(models.Reservation)
                .filter(
                    models.Reservation.annonce_id == annonce.id,
                    models.Reservation.created_at < snapshot,
                    models.Reservation.status == "confirmed"
                )
                .scalar()
            )

            kilos_total = annonce.kilos_disponibles
            kilos_remaining = max(kilos_total - reserved_before, 0)
            occupancy_rate = reserved_before / kilos_total if kilos_total > 0 else 0

            # 2️⃣ kilos réservés DANS LES 7 JOURS APRÈS snapshot (TARGET)
            kilos_next_7d = (
                db.query(func.coalesce(func.sum(models.ReservationItem.weight), 0))
                .join(models.Reservation)
                .filter(
                    models.Reservation.annonce_id == annonce.id,
                    models.Reservation.created_at >= snapshot,
                    models.Reservation.created_at < snapshot + timedelta(days=7),
                    models.Reservation.status == "confirmed"
                )
                .scalar()
            )

            # 3️⃣ Historique
            hist_7d = (
                db.query(func.coalesce(func.sum(models.ReservationItem.weight), 0))
                .join(models.Reservation)
                .filter(
                    models.Reservation.annonce_id == annonce.id,
                    models.Reservation.created_at >= snapshot - timedelta(days=7),
                    models.Reservation.created_at < snapshot,
                    models.Reservation.status == "confirmed"
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
                    models.Reservation.status == "confirmed"
                )
                .scalar()
            )

            # 4️⃣ Popularité route
            route_30d = (
                db.query(func.count(models.Reservation.id))
                .join(models.Annonce)
                .filter(
                    models.Annonce.origin == annonce.origin,
                    models.Annonce.destination == annonce.destination,
                    models.Reservation.created_at >= snapshot - timedelta(days=30),
                    models.Reservation.status == "confirmed"
                )
                .scalar()
            )

            # 5️⃣ Temps
            days_until_departure = (annonce.date_depart - snapshot).days

            rows.append({
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

                "days_until_departure": days_until_departure,
                "dow_snapshot": snapshot.weekday(),
                "is_weekend": snapshot.weekday() >= 5,
                "month_snapshot": snapshot.month,

                "hist_bookings_7d": hist_7d,
                "hist_bookings_30d": hist_30d,
                "route_bookings_30d": route_30d,

                "kilos_booked_next_7d": kilos_next_7d
            })

    return pd.DataFrame(rows)
if __name__ == "__main__":
    db = SessionLocal()
    try:
        df = build_ml_dataset(db)
        df.to_csv("./mls/datas/ml_annonce_demand_dataset.csv", index=False)
        print("Dataset généré :", df.shape)
    finally:
        db.close()
