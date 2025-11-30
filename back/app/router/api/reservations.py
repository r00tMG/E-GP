from datetime import datetime, timedelta, timezone
from typing import List

from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session, joinedload

from app.databases.database import get_db
from app.models import models
from app.schemas.schemas_reservation_create import ReservationCreate, ReservationSchemaResponse, ReservationSchemaData, \
    AnnonceBase
from app.schemas.schemas_users import UserResponse
from app.security.oauth2 import get_current_user
from app.security.token import role_required

router = APIRouter(
    tags=["Reservation"]
)


@router.post("/reservations", response_model=ReservationSchemaResponse)
async def create(
        reservation_data: ReservationCreate,
        db: Session = Depends(get_db),
        current_user: models.User = Depends(role_required("client")),
):
    #print(f"user in reservation: {current_user.id}, {current_user.email}, {current_user.role}")
    # Vérifier si l’annonce existe
    annonce = db.query(models.Annonce).filter(models.Annonce.id == reservation_data.annonce_id).first()
    if not annonce:
        raise HTTPException(status_code=404, detail="Annonce introuvable")

    # TTL pour la réservation PENDING (ex : 20 minutes)
    TTL_MINUTES = 20
    expired_at = datetime.now(timezone.utc) + timedelta(minutes=TTL_MINUTES)
    # calcul des kilos demandés
    total_kilos_requested = sum(float(item.weight) for item in (reservation_data.items or []))
    print("Kilo demandé: ", total_kilos_requested)

    # Vérifier la disponibilité
    kilos_dispo = float(annonce.kilos_disponibles or 0)
    print("Kilo disponible: ", kilos_dispo)
    if total_kilos_requested > kilos_dispo:
        raise HTTPException(status_code=400, detail=f"Pas assez de kilos disponibles. Disponibles: {kilos_dispo}")

    # Décrémenter les kilos disponibles immédiatement (hold)
    annonce.kilos_disponibles = kilos_dispo - total_kilos_requested
    db.add(annonce)  # persister changement dans la transaction

    # Créer la réservation
    reservation = models.Reservation(
        user_id=current_user.id,
        annonce_id=reservation_data.annonce_id,
        status=models.StatusReservation.PENDING,
        expired_at=expired_at
    )


    total_price = 0
    # Ajouter les marchandises au kilo
    for item in reservation_data.items:
        new_item = models.ReservationItem(
            item_name=item.item_name,
            price_per_kg=annonce.prix_du_kilo,
            weight=item.weight,
        )
        reservation.items.append(new_item)
        total_price += float(annonce.prix_du_kilo) * float(item.weight)

    # Ajouter les marchandises spéciales
    for special in reservation_data.special_items:
        new_special = models.ReservationSpecialItem(
            item_name=special.item_name,
            price_per_piece=annonce.prix_par_piece,
            quantity=special.quantity,
        )
        reservation.special_items.append(new_special)
        total_price += float(annonce.prix_par_piece) * float(special.quantity)

    reservation.total_price = total_price

    # Calcul commission + TVA
    # commission = subtotal * COMMISSION_RATE
    # tva = commission * TVA_RATE
    # total = subtotal + commission + tva
    #
    # # Affectation
    # reservation.subtotal = subtotal
    # reservation.commission = commission
    # reservation.tva = tva
    # reservation.total_price = total

    db.add(reservation)
    db.commit()
    db.refresh(reservation)
    response_data = ReservationSchemaData(
        annonce=AnnonceBase(
            id=annonce.id,
            date_depart=annonce.date_depart,
            date_arrivee=annonce.date_arrivee,
            prix_par_kilo=annonce.prix_du_kilo,
            prix_par_piece=annonce.prix_par_piece,
            destination=annonce.destination,
            origin=annonce.origin
        ),
        user=UserResponse(
            id=current_user.id,
            email=current_user.email,
            role=current_user.role,
        ),
        reservation_id=reservation.id,
        total_price=reservation.total_price,
        status=reservation.status,
        items=reservation.items,
        special_items=reservation.special_items,
    )
    return {
        "status": status.HTTP_201_CREATED,
        "message": "Réservation créée avec succès",
        "reservation": response_data
    }

@router.get("/reservations")
async def indexbyuser(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    reservations = (db.query(models.Reservation)
    .options(
        joinedload(models.Reservation.user),
            joinedload(models.Reservation.annonce),
            joinedload(models.Reservation.items),
            joinedload(models.Reservation.special_items)
    ).filter(models.Reservation.user_id == current_user.id).all())

    return {
        "status": status.HTTP_200_OK,
        "message": "Réservation créée avec succès",
        "reservation": reservations
    }

@router.get("/admin/reservations")
async def index(db: Session = Depends(get_db), current_user=Depends(role_required("admin"))):
    reservations = (db.query(models.Reservation)
    .options(
        joinedload(models.Reservation.user),
            joinedload(models.Reservation.annonce),
            joinedload(models.Reservation.items),
            joinedload(models.Reservation.special_items)
    ).all())

    return {
        "status": status.HTTP_200_OK,
        "message": "Réservation créée avec succès",
        "reservation": reservations
    }


def release_expired_reservations(db: Session=Depends(get_db)):
    now = datetime.now(timezone.utc)
    expired = db.query(models.Reservation).filter(
        models.Reservation.status == models.StatusReservation.PENDING,
        models.Reservation.expires_at < now
    ).all()

    for r in expired:
        try:
            annonce = db.query(models.Annonce).with_for_update().filter(models.Annonce.id == r.annonce_id).first()
            if annonce:
                annonce.kilos_disponibles = float(annonce.kilos_disponibles or 0) + float(r.kilos_reserved or 0)
                db.add(annonce)
            r.status = models.StatusReservation.CANCELLED
            db.add(r)
        except Exception as e:
            print("Erreur lors du release:", e)
