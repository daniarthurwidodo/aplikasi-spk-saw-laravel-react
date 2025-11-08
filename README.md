# 📘 SPK-SAWh - Sistem Pendukung Keputusan Sekolah Menengah Kejuruan

[![Laravel](https://img.shields.io/badge/Laravel-12.36.1-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19.2.0-61DAFB?style=flat&logo=react)](https://react.dev)
[![PHP](https://img.shields.io/badge/PHP-8.4.11-777BB4?style=flat&logo=php)](https://www.php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=flat&logo=postgresql)](https://www.postgresql.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

**SPK-SAWh** is a comprehensive decision support system designed for Indonesian vocational schools (SMK) that integrates data analytics, strategic recommendations, and task management using the **Simple Additive Weighting (SAW)** method.

> 📖 **Quick Links**: [Quick Start](QUICKSTART.md) | [Docker Setup](DOCKER.md) | [Documentation](#-documentation) | [Contributing](CONTRIBUTING.md) | [Changelog](CHANGELOG.md)

## 🎯 Overview

SPK-SAWh helps vocational schools make data-driven strategic decisions by:
- **Automating school performance evaluation** based on multi-source data (Dapodik, RKAS, Sarpras)
- **Converting analysis results into actionable tasks** for school management
- **Providing transparent reporting** to education authorities
- **Enabling strategic planning** through data analytics and SAW methodology

### 🏆 Key Features

✅ **Multi-Role Access Control** - Super Admin, Admin, Kepala Sekolah, Staff  
✅ **SAW Decision Engine** - Automated scoring and ranking system  
✅ **Data Import System** - CSV/XLSX import from Dapodik, RKAS, Sarpras  
✅ **Task Management** - Kanban-style workflow with approval process  
✅ **Real-time Dashboard** - Performance monitoring with radar charts  
✅ **Report Generation** - Automated PDF/Excel reports  
✅ **File Management** - Secure upload and storage system  

## 🔐 Current Status: Authentication Module Complete

✅ **MAJOR MILESTONE ACHIEVED**: Complete authentication foundation has been implemented and tested.

### 🧪 Test the Authentication
```bash
# Test the complete setup
php artisan auth:test

# Sample login credentials
# Super Admin: superadmin@spksaw.com / password123
# School Admin: admin1@spksaw.com / password123
```

### 📊 Current Database
- **5 Schools**: Realistic data from Aceh, Jakarta, Semarang, Surabaya
- **27 Users**: Complete hierarchy with proper role distribution
- **Full Relationships**: Users ↔ Schools, Kepala Sekolah assignments

## 🚀 Tech Stack

### Backend

- **Laravel 12.36.1** - PHP Framework
- **PHP 8.4.11** - Programming Language  
- **PostgreSQL 16** - Database
- **JWT Authentication** - Secure token-based auth
- **Redis + BullMQ** - Job Queue System

### Frontend

- **React 19.2.0** - UI Library
- **React Router DOM 7.9.5** - Client-side Routing
- **Vite 7.0.7** - Build Tool
- **Tailwind CSS 4.0** - CSS Framework

### DevOps & Storage

- **Docker/Podman** - Container Management
- **MinIO** - S3-compatible Object Storage
- **pgAdmin 4** - Database Management Tool

## 👥 User Roles & Permissions

| Role | Description | Key Permissions |
|------|-------------|-----------------|
| **Super Admin** | Central controller for all schools and users | Manage schools, roles, weights, audit logs, system settings |
| **Admin** | School-level operator managing imports and analytics | Upload & validate data, trigger SAW computation, generate reports |
| **Kepala Sekolah** | Principal with oversight and approval power | View dashboards, approve/reject tasks, create strategic recommendations |
| **User (Staff)** | School staff performing operational tasks | Create and execute tasks, manage subtasks and uploads |

## 🧩 Core Modules

### 🔐 Authentication & User Management
- JWT-based authentication with refresh tokens
- Role-based authorization middleware
- CRUD for users and school assignments
- Password reset and account status management

### 🏫 School Management  
- Manage school profiles, accreditation, and metadata
- Assign principals (kepala_sekolah_user_id)
- View all users per school

### ⚖️ SAW Decision Engine
- Manage kriteria and subkriteria
- Approve or update bobot with audit tracking
- Normalize raw data and compute SAW results
- Generate ranking and export analysis reports

### 📥 Data Import & Mapping
- Upload CSV/XLSX data from Dapodik, RKAS, Sarpras
- Map spreadsheet columns to subkriteria
- Validate and parse data with live preview
- Automatically normalize and store results

### 🧩 Task & Subtask Management
- Kanban-based workflow: `created → in_progress → pending_approval → approved/rejected → done → reported`
- Subtasks with attachments, status, and comments
- Approval process by principal
- Automatic report generation after approval

### 📎 File Management & Storage
- Upload and preview files (multi-form support)
- Store metadata (owner, related_table, related_id)
- Support images, PDFs, spreadsheets
- Generate thumbnails for images
- Access control per user role

### 📊 Reports & Dashboard
- Dashboard summary per bidang (A–G categories)
- View SAW results (scores, ranking, trend)
- Generate PDF/Excel reports
- Real-time task summary and progress visualization

## 🚀 Available API Endpoints

### 🔐 Authentication
- `POST /api/auth/login` - User authentication ✅
- `POST /api/auth/logout` - Secure logout ✅  
- `POST /api/auth/refresh` - Token refresh ✅
- `GET /api/auth/me` - Current user info ✅

### 🏫 School Management
- `GET /api/schools` - List schools
- `POST /api/schools` - Add new school  
- `PATCH /api/schools/:id` - Update school data
- `GET /api/schools/:id/users` - View school members

### ⚖️ SAW Engine (Planned)
- `GET /api/kriteria` - Get criteria list
- `POST /api/kriteria` - Add new criteria
- `GET /api/bobot` - Get active weights
- `POST /api/bobot/approve` - Approve weights
- `POST /api/saw/compute` - Run normalization & SAW
- `GET /api/saw/results` - View scores & rankings

📖 **Complete API documentation**: [API Reference](docs/api-reference.md)

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 8.2 with extensions:
  - PDO
  - pgsql
  - mbstring
  - xml
  - curl
- **Composer** - PHP package manager
- **Node.js** >= 20.x - JavaScript runtime
- **npm** or **yarn** - Package manager
- **Docker Desktop** or **Podman** - Container runtime

## 🛠️ Installation

### Quick Setup

For first-time setup, run the automated setup script:

```bash
composer setup
```

This will:
- Install PHP dependencies
- Copy `.env.example` to `.env`
- Generate application key
- Run database migrations
- Install Node.js dependencies
- Build frontend assets

### Manual Setup

If you prefer manual setup:

1. **Clone the repository**
   ```bash
   git clone https://github.com/daniarthurwidodo/aplikasi-spk-saw-laravel-react.git
   cd aplikasi-spk-saw-laravel-react
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Start PostgreSQL with Docker/Podman**
   ```bash
   # Using Docker
   docker-compose up -d
   
   # Using Podman
   podman compose up -d
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Install Node.js dependencies**
   ```bash
   npm install
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

## 🚀 Running the Application

### Development Mode

The easiest way to run the application in development mode:

```bash
composer dev
```

This starts all necessary services concurrently:
- Laravel development server (http://localhost:8000)
- Queue worker
- Laravel Pail (logs viewer)
- Vite dev server (HMR)

### Manual Development Mode

Alternatively, run services separately:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```

**Terminal 3 - Queue Worker (optional):**
```bash
php artisan queue:work
```

### Production Build

```bash
npm run build
php artisan serve
```

## 🗄️ Database Management

### Docker/Podman Commands

**Start containers:**
```bash
# Docker
docker-compose up -d

# Podman
podman compose up -d
```

**Stop containers:**
```bash
# Docker
docker-compose down

# Podman
podman compose down
```

**Access PostgreSQL CLI:**
```bash
# Docker
docker-compose exec postgres psql -U laravel -d laravel

# Podman
podman exec -it laravel_postgres psql -U laravel -d laravel
```

### Database Configuration

Default database credentials (configurable in `.env`):

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### pgAdmin Access

Access the web-based database manager:
- URL: http://localhost:5050
- Email: `admin@admin.com`
- Password: `admin`

See [DOCKER.md](DOCKER.md) for detailed Docker/Podman documentation.

## 🧪 Testing

### Authentication System Testing

Test the complete authentication setup:

```bash
# Run authentication system verification
php artisan auth:test
```

This will verify:
- Database connectivity and seeded data
- User roles and permissions  
- JWT configuration
- Password verification
- School relationships

### General Testing

Run the test suite:

```bash
composer test
```

Or run PHPUnit directly:

```bash
php artisan test
```

## 📁 Project Structure

```
├── app/                     # Application logic
│   ├── Http/               # Controllers, Middleware
│   │   └── Controllers/
│   │       └── Auth/       # ✅ Authentication controllers
│   ├── Models/             # Eloquent models
│   │   ├── User.php        # ✅ Enhanced with JWT & roles
│   │   └── School.php      # ✅ School management model
│   └── Providers/          # Service providers
├── database/               # Database files
│   ├── migrations/         # Database migrations
│   │   ├── *_create_schools_table.php           # ✅ Schools structure
│   │   ├── *_modify_users_table_for_auth.php    # ✅ Enhanced users
│   │   └── *_add_kepala_sekolah_to_schools.php  # ✅ Relationships
│   ├── seeders/           # Database seeders
│   │   ├── SchoolSeeder.php    # ✅ 5 sample schools
│   │   └── UserSeeder.php      # ✅ 27 test users
│   └── factories/         # Model factories
├── docs/                  # 📝 Documentation
│   ├── modules/           # Module-specific documentation
│   │   ├── authentication.md       # Auth system guide
│   │   ├── school-management.md    # School management
│   │   ├── saw-engine.md          # SAW algorithm implementation
│   │   ├── data-import.md         # Data import system
│   │   ├── task-management.md     # Task workflow
│   │   └── file-management.md     # File upload system
│   ├── api-reference.md           # Complete API documentation
│   ├── deployment.md              # Production deployment guide
│   └── development.md             # Development guidelines
├── public/                # Public assets
├── resources/             # Frontend resources
│   ├── css/              # Stylesheets
│   ├── js/               # React components
│   │   ├── components/   # Reusable UI components
│   │   ├── layouts/      # Layout components
│   │   ├── pages/        # Page components
│   │   └── routes.jsx    # Route definitions
│   └── views/            # Blade templates
├── routes/               # Route definitions
│   └── api.php           # ✅ Authentication API routes
├── storage/              # Application storage
├── tests/                # Test files
└── docker-compose.yml    # Container configuration
```

## 🗓️ Development Roadmap

| Milestone | Status | Description |
|-----------|--------|-------------|
| **M1** | ✅ **COMPLETE** | Auth + RBAC + CRUD Schools & Users |
| **M2** | 🚧 **IN PROGRESS** | Data Import System + Mapping |
| **M3** | 📋 **PLANNED** | SAW Engine + Normalization |
| **M4** | 📋 **PLANNED** | Dashboard + Alternatif Management |
| **M5** | 📋 **PLANNED** | Task + Subtask Workflow + Approval |
| **M6** | 📋 **PLANNED** | Reporting + Bobot History + Audit Logs |
| **M7** | 📋 **PLANNED** | Monitoring + S3 Storage + Production Hardening |

### 🎯 Current Phase: M2 - Data Import System

**Next Features in Development:**
- CSV/XLSX file upload and validation
- Column mapping interface for Dapodik/RKAS/Sarpras data
- Data normalization pipeline
- Import status tracking and error handling

### 🚀 Success Metrics
- ✅ 95% import success rate for CSV/XLSX data
- ✅ SAW computation < 3 seconds per school
- ✅ Task completion rate tracking
- ✅ Monthly report generation automated

## 🔧 Available Commands

### Quick Commands with Make

For convenience, you can use Make commands (recommended):

```bash
make help           # Show all available commands
make setup          # First-time setup
make dev            # Start development server
make start          # Start containers
make stop           # Stop containers
make db-migrate     # Run migrations
make db-fresh       # Fresh migration + seed
make test           # Run tests
make clean          # Clear all caches
```

Run `make help` to see all available commands.

### Composer Scripts

```bash
composer setup          # First-time setup
composer dev           # Run development server with all services
composer test          # Run tests
```

### NPM Scripts

```bash
npm run dev           # Start Vite dev server
npm run build         # Build for production
```

### Artisan Commands

```bash
php artisan serve             # Start development server
php artisan migrate           # Run migrations
php artisan migrate:fresh     # Fresh migration
php artisan db:seed          # Seed database
php artisan queue:work       # Run queue worker
php artisan pail             # View logs
php artisan db:show          # Show database info
```

## 🌐 Accessing the Application

- **Application**: http://localhost:8000
- **pgAdmin**: http://localhost:5050
- **Vite Dev Server**: http://localhost:5173 (when running `npm run dev`)

## 🐛 Troubleshooting

### Vite Manifest Not Found

If you see "Vite manifest not found" error:

```bash
npm run dev
```

Or build the assets:

```bash
npm run build
```

### Database Connection Issues

1. Check if PostgreSQL container is running:
   ```bash
   # Docker
   docker-compose ps
   
   # Podman
   podman ps
   ```

2. Verify database credentials in `.env` match `docker-compose.yml`

3. Test database connection:
   ```bash
   php artisan db:show
   ```

### Port Already in Use

Change ports in `.env`:

```env
DB_PORT=5433
PGADMIN_PORT=5051
```

Then restart containers.

## 📚 Documentation

### Core Documentation

- **[Quick Start Guide](QUICKSTART.md)** - Get up and running in 5 minutes
- **[Docker Setup](DOCKER.md)** - Container deployment guide
- **[Router Setup](ROUTER.md)** - Frontend routing configuration

### Module Documentation

- **[Authentication System](docs/modules/authentication.md)** - Complete auth implementation guide
- **[School Management](docs/modules/school-management.md)** - School CRUD and relationship management
- **[SAW Decision Engine](docs/modules/saw-engine.md)** - Algorithm implementation and usage
- **[Data Import System](docs/modules/data-import.md)** - CSV/XLSX import and mapping
- **[Task Management](docs/modules/task-management.md)** - Workflow and approval system
- **[File Management](docs/modules/file-management.md)** - Upload and storage system

### API & Development

- **[API Reference](docs/api-reference.md)** - Complete endpoint documentation
- **[Development Guide](docs/development.md)** - Setup and contribution guidelines
- **[Deployment Guide](docs/deployment.md)** - Production deployment instructions

### External Resources

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev)
- [React Router Documentation](https://reactrouter.com/)
- [Vite Documentation](https://vitejs.dev)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
