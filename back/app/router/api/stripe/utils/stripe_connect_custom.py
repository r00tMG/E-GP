from fastapi import APIRouter, HTTPException
import stripe
import os
stripe.api_key = os.getenv("STRIPE_SECRET_KEY")

async def create_connected_account(email: str):
    try:
        account = stripe.Account.create(
            type="custom",
            country="FR",
            email=email,
            capabilities={
                "card_payments": {"requested": True},
                "transfers": {"requested": True},
            },
            business_type="individual",
        )
        return {"account_id": account.id}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))
