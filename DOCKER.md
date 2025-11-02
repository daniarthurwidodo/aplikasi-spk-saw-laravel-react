# Docker Setup Guide

## PostgreSQL Database with Docker/Podman

This project includes a Docker Compose configuration for running PostgreSQL and pgAdmin.

### Prerequisites

- **Docker Desktop** or **Podman** installed on your machine
- Docker Compose (included with Docker Desktop) or `podman-compose`

### Using Podman

If you're using Podman instead of Docker, use `podman-compose` or `podman compose` commands:
- Replace `docker-compose` with `podman-compose` in all commands below
- Or use `podman compose` (Podman 4.0+)
- Ensure your Podman machine is running: `podman machine start`

### Services Included

1. **PostgreSQL 16** - Main database server
   - Port: `5432` (configurable via `DB_PORT` in `.env`)
   - Database: `laravel` (configurable via `DB_DATABASE`)
   - Username: `laravel` (configurable via `DB_USERNAME`)
   - Password: `secret` (configurable via `DB_PASSWORD`)

2. **pgAdmin 4** - Web-based PostgreSQL management tool (optional)
   - Port: `5050` (configurable via `PGADMIN_PORT` in `.env`)
   - Email: `admin@admin.com` (configurable via `PGADMIN_EMAIL`)
   - Password: `admin` (configurable via `PGADMIN_PASSWORD`)

### Quick Start

1. **Start the containers:**
   ```bash
   # Using Docker
   docker-compose up -d
   
   # Using Podman
   podman-compose up -d
   # or
   podman compose up -d
   ```

2. **Verify containers are running:**
   ```bash
   # Using Docker
   docker-compose ps
   
   # Using Podman
   podman ps
   ```

3. **Run migrations:**
   ```bash
   php artisan migrate
   ```

### Useful Commands

**Start containers:**
```bash
# Docker
docker-compose up -d

# Podman
podman-compose up -d
# or
podman compose up -d
```

**Stop containers:**
```bash
# Docker
docker-compose down

# Podman
podman-compose down
# or
podman compose down
```

**Stop containers and remove volumes (⚠️ deletes all data):**
```bash
# Docker
docker-compose down -v

# Podman
podman-compose down -v
# or
podman compose down -v
```

**View container logs:**
```bash
# Docker - All services
docker-compose logs -f

# Docker - PostgreSQL only
docker-compose logs -f postgres

# Podman - All services
podman logs -f laravel_postgres laravel_pgadmin

# Podman - PostgreSQL only
podman logs -f laravel_postgres
```

**Restart containers:**
```bash
# Docker
docker-compose restart

# Podman
podman restart laravel_postgres laravel_pgadmin
```

**Access PostgreSQL CLI:**
```bash
# Docker
docker-compose exec postgres psql -U laravel -d laravel

# Podman
podman exec -it laravel_postgres psql -U laravel -d laravel
```

### Accessing pgAdmin

1. Open your browser and go to: `http://localhost:5050`
2. Login with:
   - Email: `admin@admin.com` (or your configured `PGADMIN_EMAIL`)
   - Password: `admin` (or your configured `PGADMIN_PASSWORD`)

3. Add a new server in pgAdmin:
   - **General Tab:**
     - Name: `Laravel` (or any name you prefer)
   
   - **Connection Tab:**
     - Host name/address: `postgres` (use container name for internal network)
     - Port: `5432`
     - Maintenance database: `laravel`
     - Username: `laravel`
     - Password: `secret`

### Configuration

All database configurations are stored in the `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

# pgAdmin Configuration
PGADMIN_PORT=5050
PGADMIN_EMAIL=admin@admin.com
PGADMIN_PASSWORD=admin
```

### Troubleshooting

**Port already in use:**
If port 5432 or 5050 is already in use, change the ports in your `.env` file:
```env
DB_PORT=5433
PGADMIN_PORT=5051
```

**Connection refused:**
- Ensure containers are running: `docker-compose ps` or `podman ps`
- Check container health: 
  - Docker: `docker-compose exec postgres pg_isready -U laravel`
  - Podman: `podman exec laravel_postgres pg_isready -U laravel`
- Verify `.env` database credentials match docker-compose.yml

**Podman machine not running:**
```bash
podman machine start
```

**Data persistence:**
Data is stored in volumes:
- `postgres_data` - PostgreSQL database files
- `pgadmin_data` - pgAdmin configuration

To backup your database:
```bash
# Docker
docker-compose exec postgres pg_dump -U laravel laravel > backup.sql

# Podman
podman exec laravel_postgres pg_dump -U laravel laravel > backup.sql
```

To restore from backup:
```bash
# Docker
docker-compose exec -T postgres psql -U laravel laravel < backup.sql

# Podman
podman exec -i laravel_postgres psql -U laravel laravel < backup.sql
```

### Removing pgAdmin (Optional)

If you don't need pgAdmin, you can comment out or remove the `pgadmin` service from `docker-compose.yml` and start only PostgreSQL:

```bash
docker-compose up -d postgres
```
