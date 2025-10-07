from datetime import datetime

from pydantic import BaseModel
class Token(BaseModel):
    access_token: str
    token_type: str


class TokenData(BaseModel):
    id: int | None = None
    first_name:str | None = None
    last_name:str | None = None
    email: str | None = None
    role: str | None = None
