from fastapi import Request, status
from fastapi.responses import RedirectResponse
from functools import wraps

def login_required(func):
    @wraps(func)
    async def wrapper(request: Request, *args, **kwargs):
        if not request.cookies.get("token"):
            return RedirectResponse(url="/web/login", status_code=status.HTTP_303_SEE_OTHER)
        return await func(request, *args, **kwargs)
    return wrapper