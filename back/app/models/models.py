import enum
from datetime import datetime

from sqlalchemy import Column, Integer, String, DateTime, func, Enum, Double, ForeignKey, Numeric
from sqlalchemy.orm import relationship

from app.databases.database import Base

class UserRole(str, enum.Enum):
    ADMIN = "admin"
    CLIENT = "client"
    GP = "gp"

class User(Base):
    __tablename__="users"
    id = Column(Integer, primary_key=True, index=True)
    first_name = Column(String)
    last_name = Column(String)
    email = Column(String)
    phone = Column(String)
    password = Column(String)
    role = Column(Enum(UserRole))
    stripe_account_id=Column(String, nullable=True)
    created_at = Column(DateTime(timezone=True), server_default=func.now())

    #Relations
    annonces = relationship("Annonce", back_populates="gp", cascade="all, delete-orphan")
    reservations = relationship("Reservation", back_populates="user")

class Annonce(Base):
    __tablename__="annonces"
    id=Column(Integer, primary_key=True, index=True)
    gp_id=Column(Integer, ForeignKey("users.id", ondelete="CASCADE"))
    kilos_disponibles=Column(Integer,)
    date_depart=Column(DateTime)
    date_arrivee=Column(DateTime)
    description=Column(String)
    prix_du_kilo=Column(Double)
    prix_par_piece=Column(Double)
    origin=Column(String)
    destination=Column(String)


    #Relations
    gp = relationship("User", back_populates="annonces")
    reservations = relationship("Reservation", back_populates="annonce")

class StatusReservation(str, enum.Enum):
    PENDING = "pending"
    CONFIRMED = "confirmed"
    CANCELLED = "cancelled"

class Reservation(Base):
    __tablename__ = "reservations"

    id = Column(Integer, primary_key=True)
    user_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"), nullable=False)
    annonce_id = Column(Integer, ForeignKey("annonces.id", ondelete="CASCADE"), nullable=False)
    expired_at = Column(DateTime, nullable=True)
    # subtotal = Column(Numeric(10, 2), default=0)  # total HT
    # commission = Column(Numeric(10, 2), default=0)  # commission plateforme
    # tva = Column(Numeric(10, 2), default=0)  # TVA sur la commission

    total_price = Column(Numeric(10, 2), default=0)
    status = Column(Enum(StatusReservation), default=StatusReservation.PENDING)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Relations
    user = relationship("User", back_populates="reservations")
    annonce = relationship("Annonce", back_populates="reservations")
    items = relationship("ReservationItem", back_populates="reservation", cascade="all, delete-orphan")
    special_items = relationship("ReservationSpecialItem", back_populates="reservation", cascade="all, delete-orphan")
    payment = relationship("Payment", back_populates="reservation", uselist=False, cascade="all, delete-orphan")


class ReservationItem(Base):
    __tablename__ = "reservation_items"

    id = Column(Integer, primary_key=True)
    reservation_id = Column(Integer, ForeignKey("reservations.id", ondelete="CASCADE"), nullable=False)
    item_name = Column(String(255), nullable=False)
    price_per_kg = Column(Numeric(10, 2), nullable=False)
    weight = Column(Numeric(10, 2), nullable=False)

    reservation = relationship("Reservation", back_populates="items")


class ReservationSpecialItem(Base):
    __tablename__ = "reservation_special_items"

    id = Column(Integer, primary_key=True)
    reservation_id = Column(Integer, ForeignKey("reservations.id", ondelete="CASCADE"), nullable=False)
    item_name = Column(String(255), nullable=False)
    price_per_piece = Column(Numeric(10, 2), nullable=False)
    quantity = Column(Integer, default=1)

    reservation = relationship("Reservation", back_populates="special_items")


class PaymentStatus(str, enum.Enum):
    PENDING = "pending"
    SUCCEEDED = "succeeded"
    FAILED = "failed"
    REFUNDED = "refunded"


class Payment(Base):
    __tablename__ = "payments"

    id = Column(Integer, primary_key=True, index=True)
    reservation_id = Column(Integer, ForeignKey("reservations.id", ondelete="CASCADE"), nullable=False)
    amount = Column(Numeric(10, 2))
    commission = Column(Numeric(10, 2))
    status = Column(Enum(PaymentStatus), default=PaymentStatus.PENDING)
    stripe_session_id = Column(String, unique=True)
    created_at = Column(DateTime, server_default=func.now())
    reservation = relationship("Reservation", back_populates="payment")
