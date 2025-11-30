from fastapi import APIRouter, Request, status
from fastapi.responses import RedirectResponse
from app.security.utils import login_required

router = APIRouter()
@router.get("/logout")
@login_required
async def logout(request: Request):
    response = RedirectResponse(url="/web/login", status_code=status.HTTP_303_SEE_OTHER)

    # Supprimer cookies
    response.delete_cookie("access_token")
    #response.delete_cookie("user_id")

    return response