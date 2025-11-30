from datetime import timedelta

from fastapi import APIRouter, Body, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.orm import Session

from app.config.hash import verify_password
from app.databases.database import get_db
from app.models import models
from app.schemas.schemas_user_login import UserLoginSchemas
from app.security.token import create_access_token, ACCESS_TOKEN_EXPIRE_MINUTES

router = APIRouter(
    tags=['Guest']
)
@router.post("/login")
async def login(request:UserLoginSchemas = Body(...), db: Session=Depends(get_db)):
    user = db.query(models.User).filter(models.User.email == request.email).first()
    if not user:
        raise HTTPException(
            status_code=status.HTTP_303_SEE_OTHER,
            detail="Vos données sont incorrectes"
        )
    if not request.email or not request.password:
        raise HTTPException(status_code=status.HTTP_400_BAD_REQUEST, detail="Veuillez remplir les champs")
    if (request.email == user.email) and verify_password(request.password, user.password):
        access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        access_token = create_access_token(
            data={"sub": user.email, "role":user.role, "id":user.id, "first_name":user.first_name, "last_name":user.last_name}, expires_delta=access_token_expires
        )
        return {"access_token":access_token, "token_type":"bearer", "role":user.role}
    else:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="Impossible de vous connecté, veuillez remplir les valeurs correctes")
