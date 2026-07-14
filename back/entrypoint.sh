#!/bin/sh

set -e

echo "⏳ Attente de PostgreSQL..."

while ! python -c "
import socket
s = socket.socket()
try:
    s.connect(('db',5432))
    print('OK')
except:
    raise SystemExit(1)
"; do
    sleep 2
done

echo "✅ PostgreSQL disponible"

echo "📦 Application des migrations..."
alembic upgrade head

echo "🚀 Démarrage de FastAPI"

exec uvicorn main:app --host 0.0.0.0 --port 8000 --reload