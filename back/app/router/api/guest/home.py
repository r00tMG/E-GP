from fastapi import APIRouter

router = APIRouter(
    tags=['HomeAnnonce']
)

@router.get('/annonces')
async def getAnnonce():
    pass