from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session, joinedload

from app.databases.database import get_db
from app.dependencies_web import get_current_user
from app.models import models
from app.schemas.schemas_reservation_create import ReservationCreate, ReservationSchemaResponse, ReservationSchemaData, \
    AnnonceBase
from app.schemas.schemas_users import UserResponse
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
    print(f"user in reservation: {current_user.id}, {current_user.email}, {current_user.role}")
    # Vérifier si l’annonce existe
    annonce = db.query(models.Annonce).filter(models.Annonce.id == reservation_data.annonce_id).first()
    if not annonce:
        raise HTTPException(status_code=404, detail="Annonce introuvable")

    # Créer la réservation
    reservation = models.Reservation(
        user_id=current_user.id,
        annonce_id=reservation_data.annonce_id,
        status=models.StatusReservation.PENDING,
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

    # # 💰 Calcul commission + TVA
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
    response_date = ReservationSchemaData(
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
        total_price=reservation.total_price,
        status=reservation.status,
        items=reservation.items,
        special_items=reservation.special_items,
    )
    return {
        "status": status.HTTP_201_CREATED,
        "message": "Réservation créée avec succès",
        "reservation": response_date
    }

@router.get("/reservations")
async def index(db: Session = Depends(get_db), current_user=Depends(get_current_user)):
    reservations = (db.query(models.Reservation).options(
        joinedload(
            models.Reservation.user,
            models.Reservation.annonce,
            models.Reservation.items,
            models.Reservation.special_items))
                    .filter(models.Reservation.user.id == current_user.id).all())
    return reservations
