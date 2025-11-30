from datetime import datetime

from pydantic import BaseModel
from typing import List, Optional

from app.models.models import StatusReservation
from app.schemas.schemas_users import UserResponse


class ReservationItemCreate(BaseModel):
    item_name: str
    #price_per_kg: float
    weight: float

    class Config:
        from_attributes = True

class ReservationSpecialItemCreate(BaseModel):
    item_name: str
    #price_per_piece: float
    quantity: int = 1

    class Config:
        from_attributes = True

class ReservationCreate(BaseModel):
    annonce_id: int
    items: Optional[List[ReservationItemCreate]] = []
    special_items: Optional[List[ReservationSpecialItemCreate]] = []

    class Config:
        from_attributes = True


class AnnonceBase(BaseModel):
    id: int
    date_depart: datetime
    date_arrivee:datetime
    prix_par_kilo: float
    prix_par_piece:float
    destination: str
    origin:str

    class Config:
        from_attributes = True


class ReservationSchemaData(BaseModel):
    reservation_id: int
    annonce: Optional[AnnonceBase]
    user: Optional[UserResponse]
    total_price:float
    status:StatusReservation
    items:Optional[List[ReservationItemCreate]] = None
    special_items:Optional[List[ReservationSpecialItemCreate]] = None


class ReservationSchemaResponse(BaseModel):
    status: int
    message: str
    reservation:ReservationSchemaData

    class Config:
        from_attributes = True