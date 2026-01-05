import os

from dotenv import load_dotenv
from fastapi import FastAPI
from fastapi.params import Depends
from starlette.middleware.cors import CORSMiddleware
from starlette.middleware.sessions import SessionMiddleware
from starlette.staticfiles import StaticFiles

from app.models import models
from app.databases import database
from app.router.api import reservations, annonces
from app.router.api.guest import register, home, setRole
from app.router.api.guest import login
from app.router.api.guest import logout
from app.router.api.stripe import payments, webhook
from app.security.oauth2 import get_current_user

load_dotenv()

SECRET_KEY_MIDDLEWARE = os.getenv("SECRET_KEY_MIDDLEWARE")

models.Base.metadata.create_all(bind=database.engine)

app = FastAPI()
#Chargement des fichiers statics
app.mount("/app/static", StaticFiles(directory="./app/static"), name="static")

origins = [
    "http://localhost:5173",
    "localhost:5173"
]


app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"]
)

app.add_middleware(SessionMiddleware, secret_key=SECRET_KEY_MIDDLEWARE)

#Puplic API
#Guest
app.include_router(register.router, prefix="/api")
app.include_router(login.router, prefix="/api")
app.include_router(home.router, prefix="/api")
app.include_router(setRole.router, prefix="/api")

#Private API
app.include_router(logout.router, prefix="/api", dependencies=[Depends(get_current_user)])

#GP
app.include_router(annonces.router, prefix="/api", dependencies=[Depends(get_current_user)])

#Client
app.include_router(reservations.router, prefix="/api", dependencies=[Depends(get_current_user)])

#Admin

# Paiement
app.include_router(payments.router, prefix="/api", dependencies=[Depends(get_current_user)])
app.include_router(
    webhook.router,
    #prefix="/api",
    #dependencies=[Depends(get_current_user)]
)

