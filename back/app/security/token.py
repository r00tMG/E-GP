from datetime import timedelta, datetime, timezone

from decouple import config
from fastapi import HTTPException, status
from fastapi import Depends
from jose import jwt, JWTError
from sqlalchemy.orm import Session

from app.databases.database import get_db
from app.models import models
from app.schemas.schemas_jwt_token import TokenData
from app.security.oauth2 import get_current_user

SECRET_KEY=config("SECRET")
ALGORITHM=config("ALGORITHM")
ACCESS_TOKEN_EXPIRE_MINUTES=int(config('ACCESS_TOKEN_EXPIRE_MINUTES'))

def create_access_token(data: dict, expires_delta: timedelta | None = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.now(timezone.utc) + expires_delta
    else:
        expire = datetime.now(timezone.utc) + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

def verify_token(token:str, credentials_exception, db):
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        email = payload.get("sub")
        role: str = payload.get("role")
        id: int = payload.get("id")
        first_name:str = payload.get("first_name")
        last_name:str = payload.get("last_name")
        last_name:str = payload.get("last_name")
        #"id":user.id, "first_name":user.first_name, "last_name":user.last_name, "created_at":user.created_at
        print(f"on verify token function, email: {email}, role: {role}")
        if email is None or role is None:
            raise credentials_exception
        user = db.query(models.User).filter(models.User.email == email).first()
        if user is None:
            raise credentials_exception

        # Injecte le rôle depuis le token
        user.role = role
        user.id = id
        token_data = TokenData(id=id, email=email, role=role)
        print(f"on verify token function, token data: {token_data}")
        return token_data
    except JWTError:
        raise credentials_exception

def role_required(*allowed_roles):
    def wrapper(current_user: TokenData = Depends(get_current_user)):
        if current_user.role not in allowed_roles:
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail="Permission denied"
            )
        print(f"on role required function, current user, email: {current_user.email}, role: {current_user.role}")
        return current_user
    return wrapper
