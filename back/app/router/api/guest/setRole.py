from datetime import datetime, timezone, timedelta

from fastapi import APIRouter, Request
from fastapi.responses import JSONResponse, RedirectResponse

router = APIRouter()

@router.post("/set-role")
async def set_role_endpoint(request: Request):
    expires = datetime.now(timezone.utc) + timedelta(minutes=15)
    body = await request.json()
    role = body.get("role")
    if role not in ("gp", "client"):
        return JSONResponse({"error": "invalid role"}, status_code=400)
    print(f"role de set-role: {role}")
    # redirection vers /register et set cookie
    resp = RedirectResponse(url="/web/register", status_code=302)
    # set_cookie args: key, value, max_age (s), path, secure, httponly, samesite
    resp.set_cookie(
        key="role",
        value=role,
        path="/",
        secure=False,
        httponly=True,
        expires=expires,
        samesite="lax"
    )
    print(f"resp: {resp}" )
    return resp
