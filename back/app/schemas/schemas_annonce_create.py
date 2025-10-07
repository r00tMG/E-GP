from typing import Optional, List

from pydantic import BaseModel, Field
from datetime import datetime
from app.schemas.schemas_user_register import UserRegisterResponseData
from app.schemas.schemas_users import UserResponse


# Schema création d'annonces
class AnnonceCreateSchemas(BaseModel):
    kilos_disponibles:int
    date_depart:datetime
    date_arrivee:datetime
    description:str
    prix_du_kilo:float
    origin:str
    destination:str

class AnnonceCreateSchemasData(BaseModel):
    gp_id:int
    kilos_disponibles:int
    date_depart:datetime
    date_arrivee:datetime
    description:str
    prix_du_kilo:float
    origin:str
    destination:str

class AnnonceCreateSchemasResponse(BaseModel):
    status:int
    message:str
    annonce:AnnonceCreateSchemasData

# Schema suppression d'annonce

class AnnonceDeleteSchemaResponce(BaseModel):
    status:int
    message:str

# Schema detail annonces
class AnnonceShowSchemaData(BaseModel):
    gp: UserRegisterResponseData
    kilos_disponibles: int
    date_depart: datetime
    date_arrivee: datetime
    description: str
    prix_du_kilo: float
    prix_par_piece: float
    origin: str
    destination: str

class AnnonceShowSchemaResponse(BaseModel):
    status: int
    message: str
    annonce: Optional[AnnonceShowSchemaData]

#Schema response update annonce
class AnnonceUpdateSchemas(BaseModel):
    kilos_disponibles:Optional[int] = None
    date_depart:Optional[datetime] = None
    date_arrivee:Optional[datetime] = None
    description:Optional[str] = None
    prix_du_kilo:Optional[float] = None
    origin:Optional[str] = None
    destination:Optional[str] = None

class AnnonceUpdateSchemasData(BaseModel):
    kilos_disponibles:int
    date_depart:datetime
    date_arrivee:datetime
    description:str
    prix_du_kilo:float
    origin:str
    destination:str

class AnnonceUpdateSchemaResponse(BaseModel):
    status: int
    message: str
    annonce: AnnonceUpdateSchemasData

class AnnonceGetSchemasDatas(BaseModel):
    id:int
    kilos_disponibles: int
    date_depart: datetime
    date_arrivee: datetime
    description: str
    prix_du_kilo: float
    origin: str
    destination: str
    gp:Optional[UserResponse]
    class Config:
        from_attributes=True


class AnnonceGetSchemasResponse(BaseModel):
    status:int
    message:str
    annonces: List[AnnonceGetSchemasDatas]