from pydantic import BaseModel


class PaymentRequest(BaseModel):
    reservation_id: int
    amount: float
    receiver_id: int
    receiver_stripe_account: str