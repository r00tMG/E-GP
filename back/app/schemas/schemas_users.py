from datetime import datetime
from typing import Optional

from pydantic import BaseModel, EmailStr, Field




class UserUpdateSchemas(BaseModel):
    role: str = Field(...)

class UserLoginSchemas(BaseModel):
    email:EmailStr = Field(...)
    password:str = Field(...)

class UserResponse(BaseModel):
    id: int
    email: EmailStr = Field(None)
    role: Optional[str] = Field(None)
    created_at: datetime = Field(None)

    class Config:
        from_attributes = True

class UserResponseAfterUpdate(BaseModel):
    status: int
    message: str
    user: UserResponse

    class Config:
        from_attributes = True

