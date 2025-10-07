from pydantic import BaseModel, Field, EmailStr


class UserLoginSchemas(BaseModel):
    email:EmailStr = Field(...)
    password:str = Field(...)