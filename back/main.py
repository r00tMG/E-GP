import os

from dotenv import load_dotenv
from fastapi import FastAPI
from fastapi.params import Depends
from starlette.middleware.sessions import SessionMiddleware

from app.models import models
from app.databases import database
from app.router.api import reservations, annonces
from app.router.api.guest import register
from app.router.api.guest import login
from app.security.oauth2 import get_current_user

load_dotenv()

SECRET_KEY_MIDDLEWARE = os.getenv("SECRET_KEY_MIDDLEWARE")

models.Base.metadata.create_all(bind=database.engine)

app = FastAPI()


app.add_middleware(SessionMiddleware, secret_key=SECRET_KEY_MIDDLEWARE)
#Puplic
#Guest
app.include_router(register.router, prefix="/api")
app.include_router(login.router, prefix="/api")

#Private
#GP
app.include_router(annonces.router, prefix="/api", dependencies=[Depends(get_current_user)])

#Client
app.include_router(reservations.router, prefix="/api", dependencies=[Depends(get_current_user)])

#Admin






