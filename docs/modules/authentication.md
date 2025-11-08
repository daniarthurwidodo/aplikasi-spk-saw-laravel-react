# 🔐 Authentication System Module

**Module:** Authentication & User Management  
**Status:** ✅ Complete  
**Dependencies:** Laravel Sanctum, JWT, PostgreSQL  

## Overview

The Authentication System provides comprehensive user management with role-based access control (RBAC) for the SPK-SAWh application. It supports multiple user roles across different schools with secure JWT-based authentication.

## 👥 User Roles

| Role | Code | Description | Scope |
|------|------|-------------|-------|
| **Super Admin** | `super_admin` | System-wide administrator | All schools |
| **Admin** | `admin` | School-level data manager | Single school |
| **Kepala Sekolah** | `kepala_sekolah` | School principal | Single school |
| **User/Staff** | `user` | School staff member | Single school |

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'kepala_sekolah', 'user') NOT NULL DEFAULT 'user',
    school_id BIGINT NULL REFERENCES schools(id) ON DELETE SET NULL,
    job_title VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### Schools Table
```sql
CREATE TABLE schools (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    npsn VARCHAR(20) UNIQUE NOT NULL,
    address TEXT NULL,
    city VARCHAR(255) NULL,
    province VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    kepala_sekolah_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

## 🚀 API Endpoints

### Authentication Endpoints

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "admin1@spksaw.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 2,
            "name": "Ahmad Wijaya",
            "email": "admin1@spksaw.com",
            "role": "admin",
            "school": {
                "id": 1,
                "name": "SMK Negeri 1 Banda Aceh",
                "npsn": "10101001"
            }
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
}
```

#### Get Current User
```http
GET /api/auth/me
Authorization: Bearer {token}
```

#### Refresh Token
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### User Management Endpoints

#### List Users (Super Admin only)
```http
GET /api/users
Authorization: Bearer {token}
```

#### Create User
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "user",
    "school_id": 1,
    "job_title": "Staff Kurikulum"
}
```

#### Update User
```http
PATCH /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Doe Updated",
    "job_title": "Wakil Kepala Sekolah"
}
```

#### Delete User
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

## 🔐 Permission Matrix

| Feature | Super Admin | Admin | Kepala Sekolah | User |
|---------|-------------|-------|----------------|------|
| Manage all users | ✅ | ❌ | ❌ | ❌ |
| Manage school users | ✅ | ✅ | 👁️ View only | ❌ |
| Manage schools | ✅ | ❌ | ❌ | ❌ |
| View own profile | ✅ | ✅ | ✅ | ✅ |
| Update own profile | ✅ | ✅ | ✅ | ✅ |
| Change password | ✅ | ✅ | ✅ | ✅ |

## 🛡️ Security Features

### JWT Token Management
- **Access Token**: Short-lived (1 hour), used for API authentication
- **Refresh Token**: Long-lived (7 days), used to refresh access tokens
- **Token Blacklisting**: Logout invalidates tokens immediately

### Password Security
- **Hashing**: bcrypt with salt rounds (12)
- **Minimum Requirements**: 8 characters
- **Reset Flow**: Secure email-based password reset (planned)

### Input Validation
- **Email Validation**: RFC-compliant email validation
- **XSS Protection**: All inputs sanitized
- **SQL Injection**: Eloquent ORM prevents SQL injection
- **Rate Limiting**: Login attempts limited (planned)

## 🧪 Testing

### Test Users Available

#### Super Admin
- **Email**: `superadmin@spksaw.com`
- **Password**: `password123`
- **Access**: System-wide management

#### School Admins
- **SMK Negeri 1 Banda Aceh**: `admin1@spksaw.com`
- **SMK Negeri 1 Jakarta Pusat**: `admin2@spksaw.com`
- **SMK Negeri 1 Semarang**: `admin3@spksaw.com`
- **SMK Negeri 1 Surabaya**: `admin4@spksaw.com`
- **SMK Negeri 1 Denpasar**: `admin5@spksaw.com`

#### School Principals
- **Banda Aceh**: `kepsek1@spksaw.com`
- **Jakarta**: `kepsek2@spksaw.com`
- **Semarang**: `kepsek3@spksaw.com`
- **Surabaya**: `kepsek4@spksaw.com`
- **Denpasar**: `kepsek5@spksaw.com`

### Test Commands
```bash
# Test authentication system
php artisan auth:test

# Run authentication-specific tests
php artisan test --filter=AuthTest
```

## 🔧 Configuration

### Environment Variables
```env
# JWT Configuration
JWT_SECRET=your-secret-key-here
JWT_TTL=60  # Access token TTL in minutes
JWT_REFRESH_TTL=10080  # Refresh token TTL in minutes (7 days)

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### Middleware Configuration
- **auth:api**: JWT authentication middleware
- **role:admin**: Role-based authorization
- **throttle**: Rate limiting for auth endpoints

## 📋 Known Issues & Limitations

### Current Limitations
- Password reset via email not implemented yet
- Two-factor authentication not available
- Session-based authentication not supported (JWT only)
- User avatar/profile pictures not implemented

### Planned Improvements
- Email verification system
- Password strength requirements
- Account lockout after failed attempts
- Social media login integration
- LDAP/Active Directory integration

## 🔄 Changelog

### v1.0.0 - Authentication Foundation
- ✅ JWT-based authentication system
- ✅ Multi-role user management (4 roles)
- ✅ School-based user organization
- ✅ Complete database schema with relationships
- ✅ 27 test users across 5 schools
- ✅ API endpoints for auth and user management
- ✅ Role-based permission system

### Upcoming v1.1.0 - Enhanced Security
- 🔄 Email verification system
- 🔄 Password reset functionality
- 🔄 Rate limiting and account lockout
- 🔄 Audit logging for auth events

---

**Next Module:** [School Management](school-management.md)  
**Previous:** [Back to Documentation](../README.md)