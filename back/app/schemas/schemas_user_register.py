from typing import Optional

from pydantic import BaseModel, Field, EmailStr

from app.models.models import UserRole, User


# Schema user register
class UserRegisterSchemas(BaseModel):
    email:EmailStr = Field(...)
    password:str = Field(...)
    confirm_password:str = Field(...)
    phone: str = Field(...)
    role: UserRole = Field(...)



# Schema user register response data
class UserRegisterResponseData(BaseModel):
    first_name: str | None = None
    last_name: str | None = None
    email: EmailStr
    phone: str
    role: str

class UserRegisterSchemasResponse(BaseModel):
    status: int
    message: str
    token: dict



