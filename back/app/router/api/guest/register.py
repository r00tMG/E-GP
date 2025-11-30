from datetime import timedelta

from fastapi import APIRouter, Depends, Body, status, HTTPException
from sqlalchemy.orm import Session

from app.config.hash import hash_password
from app.databases.database import get_db
from app.models.models import User
from app.router.api.stripe.utils.stripe_connect_custom import create_connected_account
from app.schemas.schemas_user_register import UserRegisterSchemas, UserRegisterSchemasResponse
from app.security.token import ACCESS_TOKEN_EXPIRE_MINUTES, create_access_token

router=APIRouter(
    tags=['Guest']
)

@router.post("/register", response_model=UserRegisterSchemasResponse)
async def register(
    payload: UserRegisterSchemas = Body(...),
    db: Session = Depends(get_db)
):
    # Vérification de l'notebooks
    if payload.password != payload.confirm_password:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Le mot de passe et la confirmation ne correspondent pas"
        )

    # Vérifier si l'email existe déjà
    existing_user = db.query(User).filter(User.email == payload.email).first()
    if existing_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Un utilisateur avec cet email existe déjà.")

    # Créer l'utilisateur
    new_user = User(
        email=payload.email,
        password=hash_password(payload.password),
        phone=payload.phone,
        role=payload.role
    )
    db.add(new_user)
    db.commit()
    db.refresh(new_user)

    #Creation du stripe Connect Custom du gp
    if new_user.role == 'gp':
        try:
            #stripe_account = await create_connected_account(new_user.email)
            #stripe_account_id = stripe_account.get("account_id")

            # Sauvegarder l'ID Stripe dans la base
            #new_user.stripe_account_id = stripe_account_id
            print("Creation du compte stripe connect du gp")
            db.add(new_user)
            db.commit()
            db.refresh(new_user)

        except Exception as e:
            # Supprimer le user si Stripe échoue (optionnel)
            db.delete(new_user)
            db.commit()
            raise HTTPException(
                status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
                detail=f"Erreur lors de la création du compte Stripe: {str(e)}"
            )
    access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": new_user.email}, expires_delta=access_token_expires
    )
    return {
        "status": status.HTTP_201_CREATED,
        "message": "Votre inscription a réussi",
        "token": access_token
    }

