# app/api/schemas/prediction.py
from pydantic import BaseModel

class DisponibilityFeatures(BaseModel):
    kilos_total: float
    kilos_reserved_before: float
    kilos_remaining: float
    occupancy_rate: float

    days_until_departure: int
    dow_snapshot: int
    is_weekend: bool
    month_snapshot: int

    hist_bookings_7d: float
    hist_bookings_30d: float
    route_bookings_30d: int
