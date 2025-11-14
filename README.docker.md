# Docker Setup

Dieses Projekt verwendet Docker Compose um alle Services zu orchestrieren.

## Services

- **postgres**: PostgreSQL 16 Datenbank (Port 5432)
- **redis**: Redis Cache und Queue Backend (Port 6379)
- **backend**: Laravel Octane mit RoadRunner (Port 8000)
- **reverb**: Laravel Reverb WebSocket Server (Port 8080)
- **app**: Next.js Frontend (Port 3000)

## Erste Schritte

### 1. Environment-Variablen einrichten

Erstelle eine `.env` Datei im `backend/` Verzeichnis:

```bash
cp backend/.env.example backend/.env
```

Generiere einen App-Key:

```bash
docker compose run --rm backend php artisan key:generate
```

### 2. Docker Container starten

```bash
docker compose up -d
```

### 3. Datenbank Migrationen ausführen

```bash
docker compose exec backend php artisan migrate
```

### 4. (Optional) Seeder ausführen

```bash
docker compose exec backend php artisan db:seed
```

## Verfügbare URLs

- Frontend: http://localhost:3000
- Backend API: http://localhost:8000
- WebSocket Server: ws://localhost:8080

## Nützliche Kommandos

### Logs anzeigen

```bash
# Alle Services
docker compose logs -f

# Nur Backend
docker compose logs -f backend

# Nur Reverb
docker compose logs -f reverb

# Nur Frontend
docker compose logs -f app
```

### Services neustarten

```bash
# Alle Services
docker compose restart

# Einzelner Service
docker compose restart backend
```

### In Container einsteigen

```bash
# Backend
docker compose exec backend sh

# App
docker compose exec app sh
```

### Artisan Kommandos ausführen

```bash
docker compose exec backend php artisan <command>
```

### NPM Kommandos ausführen

```bash
docker compose exec app npm run <command>
```

### Services stoppen

```bash
docker compose down
```

### Services stoppen und Volumes löschen

```bash
docker compose down -v
```

## Development

Für die Entwicklung sind die Verzeichnisse als Volumes gemountet, sodass Änderungen direkt übernommen werden.

### Hot Reload

- **Next.js**: Automatisch durch Turbopack
- **Laravel Octane**: Nutze `--watch` Flag oder starte den Service neu

```bash
docker compose exec backend php artisan octane:start --watch
```

## Production

Für Production solltest du:

1. Die `APP_KEY` in docker-compose.yml durch einen sicheren Wert ersetzen
2. `APP_DEBUG=false` setzen
3. `APP_ENV=production` setzen
4. Alle Credentials ändern (DB Passwörter, Redis, etc.)
5. Den Build Command für Next.js verwenden:

```yaml
command: npm run build && npm run start
```

## Troubleshooting

### Port bereits in Verwendung

Wenn ein Port bereits verwendet wird, kannst du ihn in der `docker-compose.yml` ändern:

```yaml
ports:
  - "8001:8000"  # Ändere den ersten Port
```

### Permissions-Fehler

```bash
docker compose exec backend chown -R www-data:www-data storage bootstrap/cache
docker compose exec backend chmod -R 775 storage bootstrap/cache
```

### Container neu bauen

```bash
docker compose build --no-cache
docker compose up -d
```
