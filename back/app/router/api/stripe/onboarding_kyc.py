import stripe
from fastapi import APIRouter, HTTPException

router=APIRouter(
    tags=['Stripe']
)

@router.post("/stripe/update-account-info")
async def update_account_info(account_id: str, first_name: str, last_name: str, dob: dict, iban: str):
    try:
        account = stripe.Account.modify(
            account_id,
            individual={
                "first_name": first_name,
                "last_name": last_name,
                "dob": dob,  # {"day": 1, "month": 1, "year": 1990}
            },
            external_account={
                "object": "bank_account",
                "country": "FR",
                "currency": "eur",
                "account_number": iban,
            },
        )
        return {"status": "updated"}
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))


#Si tu veux retenir temporairement l’argent, tu peux désactiver transfer_data et plus tard faire
# stripe.Transfer.create(
#     amount=9000,  # 90 €
#     currency="eur",
#     destination=stripe_account_id,
# )
