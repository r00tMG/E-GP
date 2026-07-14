from fastapi import APIRouter, Request, status
from fastapi.responses import RedirectResponse
from app.security.utils import login_required

router = APIRouter()
@router.post("/logout")
@login_required
async def logout(request: Request):
    return {
        "status": status.HTTP_200_OK,
        "message": "Déconnexion réussie"
    }