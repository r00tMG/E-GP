from typing import List, Optional
from datetime import datetime
from fastapi import APIRouter, Depends, HTTPException, status, Request, Body, Query
from sqlalchemy import and_
from sqlalchemy.orm import Session, joinedload
from sqlalchemy.sql.functions import func

from app.databases.database import get_db
from app.models import models
from app.models.models import Annonce
from app.schemas.schemas_annonce_create import AnnonceCreateSchemas, AnnonceCreateSchemasResponse, \
    AnnonceCreateSchemasData, AnnonceDeleteSchemaResponce, AnnonceShowSchemaResponse, AnnonceUpdateSchemaResponse, \
    AnnonceUpdateSchemas, AnnonceUpdateSchemasData, AnnonceGetSchemasResponse
from app.schemas.schemas_jwt_token import TokenData
from app.security.token import role_required

router = APIRouter(
    tags=['Annonce']

)

@router.post("/annonces", response_model=AnnonceCreateSchemasResponse)
async def create(
        request: Request,
        payload: AnnonceCreateSchemas,
        db: Session = Depends(get_db),
        currentUser:TokenData = Depends(role_required("gp"))
):
    if (
            not payload.destination
            or not payload.origin
            or not payload.date_depart
            or not payload.date_arrivee
            or not payload.kilos_disponibles
            or not payload.prix_du_kilo
            or not payload.description
    ):
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Bad request"
        )
    if payload.date_depart >= payload.date_arrivee:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="La date de départ ne peux pas être aprés celle d'arrivée"
        )
    user_id = request.cookies.get("user_id")
    new_annonce = Annonce(
        gp_id=1,
        kilos_disponibles=payload.kilos_disponibles,
        date_depart=payload.date_depart,
        date_arrivee=payload.date_arrivee,
        description=payload.description,
        prix_du_kilo=payload.prix_du_kilo,
        origin=payload.origin,
        destination=payload.destination,

    )
    db.add(new_annonce)
    db.commit()
    db.refresh(new_annonce)
    response_data = AnnonceCreateSchemasData(
        gp_id=new_annonce.gp_id,
        kilos_disponibles=new_annonce.kilos_disponibles,
        date_depart=new_annonce.date_depart,
        date_arrivee=new_annonce.date_arrivee,
        description=new_annonce.description,
        prix_du_kilo=new_annonce.prix_du_kilo,
        origin=new_annonce.origin,
        destination=new_annonce.destination
    )
    return {
        "status": status.HTTP_201_CREATED,
        "message": "Annonce sauvegardé avec succés",
        "annonce": response_data
    }


@router.post("/annonce/{id}", response_model=AnnonceDeleteSchemaResponce)
async def delete(
        request: Request,
        id: int,
        db: Session = Depends(get_db),
        currentUser:TokenData = Depends(role_required("gp"))
):
    annonce = db.query(models.Annonce).filter(models.Annonce.id == id).first()
    if not annonce:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Une annonce avec cet id n'existe pas: {id}"
        )
    db.delete(annonce)
    db.commit()
    return {
        "status": status.HTTP_204_NO_CONTENT,
        "message": "Annonce supprimé avec succées"
    }


@router.get("/annonce/{id}", response_model=Optional[AnnonceShowSchemaResponse])
async def show(
        request: Request,
        id: int,
        db: Session = Depends(get_db)
):
    annonce = db.query(models.Annonce).filter(models.Annonce.id == id).first()
    if not annonce:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Une annonce avec cet id n'existe pas: {id}"
        )
    return {
        "status": status.HTTP_200_OK,
        "message": "Les informations d'une annonce",
        "annonce": annonce
    }


@router.put("/annonce/{id}", response_model=Optional[AnnonceUpdateSchemaResponse])
async def update(
        request: Request,
        id: int,
        payload: AnnonceUpdateSchemas,
        db: Session = Depends(get_db),
currentUser:TokenData = Depends(role_required("gp"))
):
    query = db.query(models.Annonce).filter(models.Annonce.id == id)
    annonce = query.first()
    if not annonce:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"Une annonce avec cet id n'existe pas: {id}"
        )

    # on récupère seulement les champs envoyés dans la requête
    update_data = payload.dict(exclude_unset=True)

    # si aucun champ à modifier
    if not update_data:
        raise HTTPException(status_code=400, detail="Aucune donnée à mettre à jour")

    query.update(update_data, synchronize_session=False)
    db.commit()
    db.refresh(annonce)
    response_data = AnnonceUpdateSchemasData(
        kilos_disponibles=annonce.kilos_disponibles,
        date_depart=annonce.date_depart,
        date_arrivee=annonce.date_arrivee,
        description=annonce.description,
        prix_du_kilo=annonce.prix_du_kilo,
        origin=annonce.origin,
        destination=annonce.destination,
    )
    return {
        "status": status.HTTP_200_OK,
        "message": "Annonce mis à jour avec succés",
        "annonce": response_data
    }

@router.get("/annonces_by_gp", response_model=AnnonceGetSchemasResponse)
async def indexByGp(
        gp_id:int=8,
        db:Session=Depends(get_db),
currentUser:TokenData = Depends(role_required("gp"))
):
    annonces = db.query(models.Annonce).filter(models.Annonce.gp_id == gp_id).all()
    if not annonces:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Ce gp n'a pas d'annonces lui correspondant: {gp_id}"
        )
    return {
        "status":status.HTTP_200_OK,
        "message":"Liste des annonces",
        "annonces":annonces
    }


@router.get("/annonces", response_model=AnnonceGetSchemasResponse)
async def index(
        search_date_depart: Optional[datetime]=Query(None),
        search_date_arrivee: Optional[datetime]=Query(None),
        search_origin: Optional[str]=Query(None),
        search_destination: Optional[str]=Query(None),
        db:Session=Depends(get_db),

):
    query = db.query(models.Annonce).options(joinedload(models.Annonce.gp))

    filters = []

    if search_date_depart:
        filters.append(func.date(models.Annonce.date_depart) == search_date_depart.date())
    if search_date_arrivee:
        filters.append(func.date(models.Annonce.date_arrivee) == search_date_arrivee.date())
    if search_origin:
        filters.append(models.Annonce.origin.ilike(f"%{search_origin}%"))
    if search_destination:
        filters.append(models.Annonce.destination.ilike(f"%{search_destination}%"))

    if filters:
        query = query.filter(and_(*filters))
       
    #annonces = query.all()
    annonces = query.order_by(models.Annonce.id.desc()).all()

    return {
        "status":status.HTTP_200_OK,
        "message":"Liste des annonces",
        "annonces":annonces
    }

