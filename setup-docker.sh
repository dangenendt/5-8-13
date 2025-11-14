#!/bin/bash

set -e

echo "🚀 Setting up Docker environment..."

# Check if .env exists in backend
if [ ! -f backend/.env ]; then
    echo "📝 Creating backend/.env from .env.example..."
    cp backend/.env.example backend/.env

    # Update .env for Docker
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/' backend/.env
    sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=postgres/' backend/.env
    sed -i 's/# DB_PORT=3306/DB_PORT=5432/' backend/.env
    sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=poker_db/' backend/.env
    sed -i 's/# DB_USERNAME=root/DB_USERNAME=poker_user/' backend/.env
    sed -i 's/# DB_PASSWORD=/DB_PASSWORD=poker_password/' backend/.env

    # Update Redis
    sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/' backend/.env

    # Update Cache and Session
    sed -i 's/CACHE_STORE=database/CACHE_STORE=redis/' backend/.env
    sed -i 's/SESSION_DRIVER=database/SESSION_DRIVER=redis/' backend/.env
    sed -i 's/QUEUE_CONNECTION=database/QUEUE_CONNECTION=redis/' backend/.env
    sed -i 's/BROADCAST_CONNECTION=log/BROADCAST_CONNECTION=reverb/' backend/.env

    echo "✅ Backend .env created and configured"
else
    echo "⚠️  backend/.env already exists, skipping..."
fi

echo ""
echo "🏗️  Building Docker images..."
docker compose build

echo ""
echo "🚀 Starting Docker containers..."
docker compose up -d

echo ""
echo "⏳ Waiting for database to be ready..."
sleep 5

echo ""
echo "🔑 Generating application key..."
docker compose exec -T backend php artisan key:generate

echo ""
echo "📦 Running database migrations..."
docker compose exec -T backend php artisan migrate --force

echo ""
echo "✅ Setup complete!"
echo ""
echo "📡 Services available at:"
echo "   Frontend:  http://localhost:3000"
echo "   Backend:   http://localhost:8000"
echo "   WebSocket: ws://localhost:8080"
echo ""
