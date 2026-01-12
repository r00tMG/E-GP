from fastapi import APIRouter, Request, Depends
from sqlalchemy.orm import Session

from app.databases.database import get_db

router = APIRouter()

@router.get("/disponiblity")
async def disponibility(request: Request, db:Session = (Depends(get_db))):
    pass