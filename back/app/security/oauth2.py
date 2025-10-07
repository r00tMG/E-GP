from typing import Annotated

from fastapi import Depends, HTTPException, status
from fastapi.security import OAuth2PasswordBearer
from sqlalchemy.orm import Session

from app.databases.database import get_db
from app.security import token

oauth2_scheme = OAuth2PasswordBearer(tokenUrl="/api/login")

async def get_current_user(data: Annotated[str, Depends(oauth2_scheme)], db:Session=Depends(get_db)):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    # user = get_user(fake_users_db, username=token_data.username)
    # if user is None:
    #     raise credentials_exception
    return token.verify_token(data, credentials_exception,db)