from fastapi import APIRouter

router = APIRouter()

@router.get("/users")
async def index():
    pass