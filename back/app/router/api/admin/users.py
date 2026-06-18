from typing import List

from fastapi import APIRouter, Request, Depends
from sqlalchemy.orm import Session
from starlette import status

from app.databases.database import get_db
from app.models import models
from app.schemas.schemas_users import UserResponse, UserResponseAfterUpdate, UserResponseIndex

router = APIRouter()

@router.get("/users", response_model=UserResponseIndex)
async def index(request:Request, db:Session=Depends(get_db)):
    users = db.query(models.User).all()
    return {
        "status":status.HTTP_200_OK,
        "message": "La liste des utilisateurs",
        "users" : users
    }
    pass
