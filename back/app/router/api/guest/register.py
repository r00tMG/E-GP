from datetime import timedelta

from fastapi import APIRouter, Depends, Body, status, HTTPException
from sqlalchemy.orm import Session

from app.config.hash import hash_password
from app.databases.database import get_db
from app.models.models import User
from app.schemas.schemas_user_register import UserRegisterSchemas, UserRegisterSchemasResponse, UserRegisterResponseData
from app.security.token import ACCESS_TOKEN_EXPIRE_MINUTES, create_access_token

router=APIRouter(
    tags=['Guest']
)

@router.post("/register", response_model=UserRegisterSchemasResponse)
async def register(
    payload: UserRegisterSchemas = Body(...),
    db: Session = Depends(get_db)
):
    # Vérification de l'input
    if payload.password != payload.confirm_password:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Le mot de passe et la confirmation ne correspondent pas"
        )

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

    access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": new_user.email}, expires_delta=access_token_expires
    )
    return {
        "status": status.HTTP_201_CREATED,
        "message": "Votre inscription a réussi",
        "token": access_token
    }

