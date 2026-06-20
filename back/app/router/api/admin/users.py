from typing import List, Optional

from fastapi import APIRouter, Request, Depends, HTTPException
from sqlalchemy.orm import Session, joinedload
from starlette import status

from app.databases.database import get_db
from app.models import models
from app.schemas.schemas_jwt_token import TokenData
from app.schemas.schemas_user_register import UserRegisterSchemas
from app.schemas.schemas_users import UserResponseIndex, UserResponse, UserResponseIn, UserResponseShow, \
    UserUpdateSchemas, UserDeleteSchemaResponce
from app.security.token import role_required

router = APIRouter(tags=["Users"])


@router.get("/users", response_model=UserResponseIndex)
async def index(request:Request, db:Session=Depends(get_db)):
    users = db.query(models.User).options(joinedload(models.User.reservations), joinedload(models.User.annonces)).all()
    return {
        "status":status.HTTP_200_OK,
        "message": "La liste des utilisateurs",
        "users" : users
    }
    pass

@router.get("/user/{id}", response_model=Optional[UserResponseShow])
async def show(request:Request, id: int, db:Session=Depends(get_db), currentUser: TokenData = Depends(role_required("admin", "gp", "client"))):
    user = db.query(models.User).options(joinedload(models.User.reservations), joinedload(models.User.annonces)).filter(models.User.id == id).first()
    return {
        "status":status.HTTP_200_OK,
        "message": f"L'utilisateur {id}",
        "user" : user
    }

@router.put("/user/{id}", response_model=Optional[UserResponseShow])
async def update(
        request: Request,
        id: int,
        payload: UserUpdateSchemas,
        db: Session = Depends(get_db),
        currentUser: TokenData = Depends(role_required("gp", "client"))
):
    print(f"L'utisateur {id}")
    query = db.query(models.User).filter(models.User.id == id)
    user = query.first()
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Un utilisateur avec cet id n'existe pas: {id}"
        )

    # on récupère seulement les champs envoyés dans la requête
    update_data = payload.dict(exclude_unset=True)

    # si aucun champ à modifier
    if not update_data:
        raise HTTPException(status_code=400, detail="Aucune donnée à mettre à jour")

    query.update(update_data, synchronize_session=False)
    db.commit()
    db.refresh(user)

    return {
        "status": status.HTTP_200_OK,
        "message": f"User {id} mis à jour avec succés",
        "user": user
    }

@router.post("/user/{id}", response_model=UserDeleteSchemaResponce)
async def delete(
        request: Request,
        id: int,
        db: Session = Depends(get_db),
        currentUser: TokenData = Depends(role_required("admin", "gp", "client"))
):
    user = db.query(models.User).filter(models.User.id == id).first()
    if not user:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Un user avec cet id n'existe pas: {id}"
        )
    db.delete(user)
    db.commit()
    return {
        "status": status.HTTP_204_NO_CONTENT,
        "message": f"User avec id={id} supprimé avec succées"
    }
