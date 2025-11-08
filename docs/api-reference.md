# 📚 API Reference Documentation

**Version:** 1.0.0  
**Base URL:** `https://api.spksaw.app` or `http://localhost:8000/api`  
**Authentication:** Bearer Token (JWT)  

## 🔐 Authentication

All API endpoints require authentication using JWT tokens, except for the login endpoint.

### Headers
```http
Authorization: Bearer {your_jwt_token}
Content-Type: application/json
Accept: application/json
```

### Token Lifecycle
- **Access Token**: 1 hour expiry
- **Refresh Token**: 7 days expiry
- **Auto-refresh**: Tokens refresh automatically when near expiry

---

## 🔑 Authentication Endpoints

### Login
```http
POST /api/auth/login
```

**Request Body:**
```json
{
    "email": "admin1@spksaw.com",
    "password": "password123"
}
```

**Response (200):**
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
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_at": "2025-11-08T15:30:22Z"
    }
}
```

### Get Current User
```http
GET /api/auth/me
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "Ahmad Wijaya",
        "email": "admin1@spksaw.com",
        "role": "admin",
        "school": {
            "id": 1,
            "name": "SMK Negeri 1 Banda Aceh"
        },
        "permissions": [
            "upload_data",
            "trigger_computation",
            "generate_reports"
        ]
    }
}
```

### Refresh Token
```http
POST /api/auth/refresh
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "expires_at": "2025-11-08T16:30:22Z"
    }
}
```

### Logout
```http
POST /api/auth/logout
```

**Response (200):**
```json
{
    "success": true,
    "message": "Successfully logged out"
}
```

---

## 🏫 School Management

### List Schools
```http
GET /api/schools
```

**Query Parameters:**
- `page` (int): Page number for pagination
- `limit` (int): Number of items per page (max 100)
- `province` (string): Filter by province
- `is_active` (boolean): Filter by active status

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "SMK Negeri 1 Banda Aceh",
            "npsn": "10101001",
            "city": "Banda Aceh",
            "province": "Aceh",
            "principal": {
                "id": 3,
                "name": "Dr. Fatimah Zahra"
            },
            "user_count": 6,
            "is_active": true,
            "created_at": "2025-11-08T10:00:00Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "total_pages": 3,
        "total_items": 25,
        "per_page": 10
    }
}
```

### Get School Details
```http
GET /api/schools/{id}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "SMK Negeri 1 Banda Aceh",
        "npsn": "10101001",
        "address": "Jl. Pendidikan No. 123",
        "city": "Banda Aceh",
        "province": "Aceh",
        "phone": "0651-1234567",
        "email": "admin@smkn1bandaaceh.sch.id",
        "accreditation": "A",
        "established_year": 1985,
        "principal": {
            "id": 3,
            "name": "Dr. Fatimah Zahra",
            "email": "kepsek1@spksaw.com"
        },
        "statistics": {
            "total_users": 6,
            "total_tasks": 15,
            "completed_tasks": 12,
            "saw_score": 0.785
        }
    }
}
```

### Create School
```http
POST /api/schools
```

**Request Body:**
```json
{
    "name": "SMK Negeri 2 Medan",
    "npsn": "12102001",
    "address": "Jl. Pendidikan No. 456",
    "city": "Medan",
    "province": "Sumatera Utara",
    "phone": "061-7654321",
    "email": "admin@smkn2medan.sch.id",
    "accreditation": "B",
    "established_year": 1990
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "School created successfully",
    "data": {
        "id": 6,
        "name": "SMK Negeri 2 Medan",
        "npsn": "12102001",
        "created_at": "2025-11-08T14:30:22Z"
    }
}
```

---

## 👥 User Management

### List Users
```http
GET /api/users
```

**Query Parameters:**
- `school_id` (int): Filter by school
- `role` (string): Filter by role (super_admin, admin, kepala_sekolah, user)
- `is_active` (boolean): Filter by active status

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 2,
            "name": "Ahmad Wijaya",
            "email": "admin1@spksaw.com",
            "role": "admin",
            "job_title": "Administrator",
            "school": {
                "id": 1,
                "name": "SMK Negeri 1 Banda Aceh"
            },
            "is_active": true,
            "last_login": "2025-11-08T13:45:00Z",
            "created_at": "2025-11-01T10:00:00Z"
        }
    ]
}
```

### Create User
```http
POST /api/users
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "user",
    "school_id": 1,
    "job_title": "Staff Kurikulum"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 28,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "school_id": 1
    }
}
```

---

## ⚖️ SAW Engine (Planned)

### List Criteria
```http
GET /api/kriteria
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode": "A1",
            "nama": "Kualitas Kepemimpinan",
            "bidang": "A",
            "tipe": "benefit",
            "subkriteria": [
                {
                    "id": 1,
                    "kode": "A1.1",
                    "nama": "Pengalaman Kepemimpinan",
                    "satuan": "tahun"
                }
            ]
        }
    ]
}
```

### Get SAW Results
```http
GET /api/saw/results
```

**Query Parameters:**
- `school_id` (int): Filter by school
- `tahun` (int): Filter by year

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "school": {
                "id": 1,
                "name": "SMK Negeri 1 Banda Aceh"
            },
            "tahun": 2025,
            "skor_total": 0.785,
            "ranking": 2,
            "skor_per_bidang": {
                "A": 0.85,
                "B": 0.72,
                "C": 0.68,
                "D": 0.91,
                "E": 0.75,
                "F": 0.78,
                "G": 0.82
            },
            "computed_at": "2025-11-08T14:30:22Z"
        }
    ]
}
```

---

## 📥 Data Import (In Progress)

### Upload Import File
```http
POST /api/imports/upload
```

**Request (multipart/form-data):**
- `file`: File to upload
- `source_type`: dapodik|rkas|sarpras|manual
- `description`: Optional description

**Response (201):**
```json
{
    "success": true,
    "data": {
        "import_id": 123,
        "filename": "dapodik_2025.xlsx",
        "total_rows": 1500,
        "status": "uploaded",
        "preview_data": [
            ["John Doe", "1234567890", "TKJ", "2023"]
        ]
    }
}
```

### Get Import Status
```http
GET /api/imports/{id}/status
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 123,
        "status": "processing",
        "progress": {
            "total_rows": 1500,
            "processed_rows": 750,
            "success_rows": 720,
            "error_rows": 30
        },
        "estimated_completion": "2025-11-08T15:00:00Z"
    }
}
```

---

## 🧩 Task Management (Planned)

### List Tasks
```http
GET /api/tasks
```

**Query Parameters:**
- `school_id` (int): Filter by school
- `status` (string): Filter by status
- `assigned_to` (int): Filter by assignee
- `priority` (string): Filter by priority

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Improve Infrastructure Quality",
            "description": "Based on SAW analysis, focus on upgrading lab equipment",
            "priority": "high",
            "status": "in_progress",
            "due_date": "2025-12-31",
            "completion_percentage": 35,
            "assignee": {
                "id": 5,
                "name": "Ahmad Santoso"
            },
            "created_at": "2025-11-01T10:00:00Z"
        }
    ]
}
```

### Create Task
```http
POST /api/tasks
```

**Request Body:**
```json
{
    "title": "Enhance Teaching Quality",
    "description": "Implement teacher training program",
    "priority": "high",
    "assigned_to": 8,
    "due_date": "2025-12-31",
    "estimated_hours": 120
}
```

---

## 📎 File Management (Planned)

### Upload File
```http
POST /api/uploads
```

**Request (multipart/form-data):**
- `file`: File to upload
- `related_table`: Optional related table
- `related_id`: Optional related record ID

**Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 456,
        "filename": "document_20251108.pdf",
        "file_size": 2048576,
        "mime_type": "application/pdf",
        "download_url": "/api/uploads/456/download"
    }
}
```

### Download File
```http
GET /api/uploads/{id}/download
```

**Response:** File download with appropriate headers

---

## 📊 Reports & Analytics

### Dashboard Summary
```http
GET /api/dashboard/summary
```

**Query Parameters:**
- `school_id` (int): Filter by school (admin+ only)
- `tahun` (int): Filter by year

**Response (200):**
```json
{
    "success": true,
    "data": {
        "school": {
            "id": 1,
            "name": "SMK Negeri 1 Banda Aceh"
        },
        "statistics": {
            "total_students": 850,
            "total_teachers": 45,
            "total_tasks": 15,
            "completed_tasks": 12,
            "saw_score": 0.785,
            "ranking": 2
        },
        "bidang_scores": {
            "A": 0.85,
            "B": 0.72,
            "C": 0.68,
            "D": 0.91,
            "E": 0.75,
            "F": 0.78,
            "G": 0.82
        },
        "recent_activities": [
            {
                "type": "task_completed",
                "description": "Infrastructure improvement task completed",
                "timestamp": "2025-11-08T10:30:00Z"
            }
        ]
    }
}
```

---

## 🚨 Error Handling

### Error Response Format
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Specific error message"]
    },
    "error_code": "VALIDATION_ERROR",
    "timestamp": "2025-11-08T14:30:22Z"
}
```

### HTTP Status Codes

| Code | Description | Usage |
|------|-------------|-------|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Missing or invalid authentication |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Common Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `AUTHENTICATION_REQUIRED` | Valid token required |
| `INSUFFICIENT_PERMISSIONS` | User lacks required permissions |
| `RESOURCE_NOT_FOUND` | Requested resource doesn't exist |
| `DUPLICATE_ENTRY` | Resource already exists |
| `RATE_LIMIT_EXCEEDED` | Too many requests |

---

## 🔄 Pagination

### Request Parameters
- `page` (int): Page number (default: 1)
- `limit` (int): Items per page (default: 10, max: 100)
- `sort` (string): Sort field
- `order` (string): Sort order (asc|desc)

### Response Format
```json
{
    "success": true,
    "data": [...],
    "pagination": {
        "current_page": 1,
        "total_pages": 5,
        "total_items": 50,
        "per_page": 10,
        "has_next": true,
        "has_prev": false
    }
}
```

---

## 🔍 Filtering & Search

### Common Query Parameters
- `search` (string): Global search term
- `filter[field]` (mixed): Filter by field value
- `date_from` (date): Start date filter
- `date_to` (date): End date filter

### Example
```http
GET /api/tasks?search=infrastructure&filter[priority]=high&date_from=2025-11-01
```

---

## 🔐 Rate Limiting

### Limits
- **Authentication**: 5 requests per minute
- **General API**: 60 requests per minute
- **File Upload**: 10 requests per minute
- **Reports**: 30 requests per minute

### Headers
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1699456800
```

---

## 🧪 Testing

### Base URLs
- **Development**: `http://localhost:8000/api`
- **Staging**: `https://staging-api.spksaw.app`
- **Production**: `https://api.spksaw.app`

### Sample cURL Commands
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin1@spksaw.com","password":"password123"}'

# Get schools
curl -X GET http://localhost:8000/api/schools \
  -H "Authorization: Bearer YOUR_TOKEN"

# Create user
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","role":"user","school_id":1}'
```

---

**Last Updated:** November 8, 2025  
**API Version:** 1.0.0  
**Documentation Version:** 1.0.0