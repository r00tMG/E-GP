from fastapi import APIRouter, Depends, status
from sqlalchemy.orm import Session, joinedload

from app.databases.database import get_db
from app.models import models
from app.schemas.schemas_annonce_create import AnnonceGetSchemasResponse

router = APIRouter(
    tags=['Guest']
)


@router.get("/home", response_model=AnnonceGetSchemasResponse)
async def index(
        db: Session = Depends(get_db)
):
    query = db.query(models.Annonce).options(joinedload(models.Annonce.gp))
    annonces = query.order_by(models.Annonce.id.desc()).limit(4)

    return {
        "status": status.HTTP_200_OK,
        "message": "Liste des annonces",
        "annonces": annonces
    }

