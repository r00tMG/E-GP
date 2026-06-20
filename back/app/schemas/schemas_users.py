from datetime import datetime
from typing import Optional, List

from pydantic import BaseModel, EmailStr, Field

from app.models.models import UserRole


class UserUpdateSchemas(BaseModel):
    role: str = Field(...)

class UserLoginSchemas(BaseModel):
    email:EmailStr = Field(...)
    password:str = Field(...)

class UserResponse(BaseModel):
    id: int
    email: EmailStr = Field(None)
    role: Optional[str] = Field(None)
    created_at: datetime = Field(None)


    class Config:
        from_attributes = True

class UserResponseAfterUpdate(BaseModel):
    status: int
    message: str
    user: UserResponse

    class Config:
        from_attributes = True

class ReservationItemResponse(BaseModel):
    item_name: str
    weight: float

    class Config:
        from_attributes = True

class SpecialItemResponse(BaseModel):
    item_name: str
    quantity: int

    class Config:
        from_attributes = True


class AnnonceResponse(BaseModel):
    id: int
    kilos_disponibles: Optional[int] = None
    date_depart: Optional[datetime] = None
    date_arrivee: Optional[datetime] = None
    description: Optional[str] = None
    prix_du_kilo: Optional[float] = None
    prix_par_piece: Optional[float] = None
    origin: Optional[str] = None
    destination: Optional[str] = None
    class Config:
        from_attributes = True

class ReservationResponse(BaseModel):
    id: int
    annonce: Optional[AnnonceResponse] = None
    total_price: Optional[float] = None
    status: Optional[str] = None
    items: List[ReservationItemResponse] = []
    special_items: List[SpecialItemResponse] = []

    class Config:
        from_attributes = True

class UserResponseIn(BaseModel):
    id: int
    email: EmailStr = Field(None)
    role: Optional[str] = Field(None)
    created_at: datetime = Field(None)
    annonces: List[AnnonceResponse] = Field(...)
    reservations: List[ReservationResponse] = Field(...)

class UserResponseIndex(BaseModel):
    status: int
    message: str
    users: List[UserResponseIn]

    class Config:
        from_attributes = True

class UserResponseShow(BaseModel):
    status: int
    message: str
    user: UserResponseIn

class UserUpdateSchemas(BaseModel):
    email: Optional[EmailStr] = None
    password: Optional[str] = None
    confirm_password: Optional[str] = None
    phone: Optional[str] = None
    role: Optional[UserRole] = None