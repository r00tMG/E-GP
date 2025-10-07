"""add column prix_par_piece to table annonces

Revision ID: e4ed5b9d647a
Revises:
Create Date: 2025-10-05 09:45:37.981062

"""
from typing import Sequence, Union

from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import postgresql

# revision identifiers, used by Alembic.
revision: str = 'e4ed5b9d647a'
down_revision: Union[str, Sequence[str], None] = None
branch_labels: Union[str, Sequence[str], None] = None
depends_on: Union[str, Sequence[str], None] = None


def upgrade():
    op.add_column(
        'annonces',
        sa.Column('prix_par_piece', sa.Float(), nullable=True)
    )

def downgrade():
    op.drop_column('annonces', 'prix_par_piece')
