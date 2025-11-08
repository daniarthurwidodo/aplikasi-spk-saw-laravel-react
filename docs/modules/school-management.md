# 🏫 School Management Module

**Module:** School Management System  
**Status:** ✅ Complete (Phase 1)  
**Dependencies:** Authentication Module, PostgreSQL  

## Overview

The School Management Module provides comprehensive management of educational institutions within the SPK-SAWh system. It handles school profiles, administrative data, and relationships with users and educational authorities.

## 🎯 Key Features

- **School Profile Management** - Complete institutional data
- **NPSN Integration** - National School Principal Number tracking
- **Principal Assignment** - Link schools with designated principals
- **Multi-Regional Support** - Schools across Indonesian provinces
- **Administrative Hierarchy** - Support for education district structure

## 🗄️ Database Schema

### Schools Table Structure
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
    website VARCHAR(255) NULL,
    accreditation ENUM('A', 'B', 'C', 'TT') NULL,
    established_year INTEGER NULL,
    kepala_sekolah_user_id BIGINT NULL REFERENCES users(id) ON DELETE SET NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### Relationships
- **One-to-Many**: School → Users (`school_id` in users table)
- **One-to-One**: School → Principal (`kepala_sekolah_user_id`)
- **Future**: School → Districts → Provinces (planned)

## 📊 Current Data

### Sample Schools (Test Data)

| ID | Name | NPSN | City | Province | Principal |
|----|------|------|------|----------|-----------|
| 1 | SMK Negeri 1 Banda Aceh | 10101001 | Banda Aceh | Aceh | Dr. Fatimah Zahra |
| 2 | SMK Negeri 1 Jakarta Pusat | 20101001 | Jakarta Pusat | DKI Jakarta | Ir. Bambang Sutrisno |
| 3 | SMK Negeri 1 Semarang | 33101001 | Semarang | Jawa Tengah | Dra. Siti Nurhaliza |
| 4 | SMK Negeri 1 Surabaya | 35101001 | Surabaya | Jawa Timur | Dr. Agus Pramono |
| 5 | SMK Negeri 1 Denpasar | 51101001 | Denpasar | Bali | I Made Sutrisna |

## 🚀 API Endpoints

### School Management

#### List All Schools
```http
GET /api/schools
Authorization: Bearer {token}
```

**Response:**
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
                "name": "Dr. Fatimah Zahra",
                "email": "kepsek1@spksaw.com"
            },
            "user_count": 6,
            "is_active": true
        }
    ]
}
```

#### Get Single School
```http
GET /api/schools/{id}
Authorization: Bearer {token}
```

#### Create New School
```http
POST /api/schools
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "SMK Negeri 2 Medan",
    "npsn": "12102001",
    "address": "Jl. Pendidikan No. 123",
    "city": "Medan",
    "province": "Sumatera Utara",
    "phone": "061-1234567",
    "email": "admin@smkn2medan.sch.id",
    "accreditation": "A",
    "established_year": 1985
}
```

#### Update School
```http
PATCH /api/schools/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "SMK Negeri 1 Banda Aceh (Updated)",
    "phone": "0651-7654321",
    "accreditation": "A"
}
```

#### Assign Principal
```http
POST /api/schools/{id}/assign-principal
Authorization: Bearer {token}
Content-Type: application/json

{
    "kepala_sekolah_user_id": 15
}
```

#### Get School Members
```http
GET /api/schools/{id}/users
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "school": {
            "id": 1,
            "name": "SMK Negeri 1 Banda Aceh"
        },
        "users": [
            {
                "id": 2,
                "name": "Ahmad Wijaya",
                "email": "admin1@spksaw.com",
                "role": "admin",
                "job_title": "Administrator"
            },
            {
                "id": 3,
                "name": "Dr. Fatimah Zahra",
                "email": "kepsek1@spksaw.com",
                "role": "kepala_sekolah",
                "job_title": "Kepala Sekolah"
            }
        ],
        "stats": {
            "total_users": 6,
            "admins": 1,
            "principals": 1,
            "staff": 4
        }
    }
}
```

#### Delete School
```http
DELETE /api/schools/{id}
Authorization: Bearer {token}
```

## 🔐 Permission Matrix

| Action | Super Admin | Admin | Kepala Sekolah | User |
|--------|-------------|-------|----------------|------|
| View all schools | ✅ | ❌ | ❌ | ❌ |
| View own school | ✅ | ✅ | ✅ | ✅ |
| Create school | ✅ | ❌ | ❌ | ❌ |
| Update school | ✅ | ❌ | ❌ | ❌ |
| Delete school | ✅ | ❌ | ❌ | ❌ |
| Assign principal | ✅ | ❌ | ❌ | ❌ |
| View school members | ✅ | ✅ | ✅ | ❌ |

## 📋 Validation Rules

### School Creation/Update
```php
'name' => 'required|string|max:255',
'npsn' => 'required|string|max:20|unique:schools,npsn',
'address' => 'nullable|string',
'city' => 'nullable|string|max:255',
'province' => 'nullable|string|max:255',
'phone' => 'nullable|string|max:20',
'email' => 'nullable|email|max:255',
'website' => 'nullable|url|max:255',
'accreditation' => 'nullable|in:A,B,C,TT',
'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
'kepala_sekolah_user_id' => 'nullable|exists:users,id'
```

### NPSN Format
- **Pattern**: Provincial code (2 digits) + School type (1 digit) + Sequential number (5 digits)
- **Example**: `20101001` (DKI Jakarta, SMK Negeri, sequence 01001)
- **Validation**: Must be unique across all schools

## 🧮 Business Logic

### Principal Assignment Rules
1. **Unique Assignment**: One principal per school
2. **Role Verification**: User must have `kepala_sekolah` role
3. **School Affiliation**: Principal must belong to the same school
4. **Auto-Update**: Previous principal assignment is automatically removed

### School Status Management
- **Active Schools**: `is_active = true` - Normal operations
- **Inactive Schools**: `is_active = false` - Archived/closed schools
- **Soft Delete**: Schools are never permanently deleted, only marked inactive

### Data Integrity
- **Cascading Updates**: User school assignments update when school changes
- **Referential Integrity**: Foreign key constraints prevent orphaned records
- **Audit Trail**: All changes logged with timestamps

## 🧪 Testing

### Test Data Verification
```bash
# Check school data integrity
php artisan db:show schools

# Verify relationships
php artisan tinker
>>> School::with(['users', 'principal'])->get()
```

### API Testing
```bash
# Test school listing (requires authentication)
curl -H "Authorization: Bearer {token}" \
     http://localhost:8000/api/schools

# Test school creation
curl -X POST \
     -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     -d '{"name":"Test School","npsn":"99999999"}' \
     http://localhost:8000/api/schools
```

## 🔧 Configuration

### Regional Settings
```php
// config/school.php (planned)
'provinces' => [
    'aceh' => 'Aceh',
    'sumut' => 'Sumatera Utara',
    'dki' => 'DKI Jakarta',
    // ... complete list
],

'accreditation_levels' => [
    'A' => 'Sangat Baik',
    'B' => 'Baik', 
    'C' => 'Cukup',
    'TT' => 'Tidak Terakreditasi'
]
```

### NPSN Code Mapping
```php
'npsn_prefixes' => [
    '11' => 'Aceh',
    '12' => 'Sumatera Utara',
    '20' => 'DKI Jakarta',
    '33' => 'Jawa Tengah',
    '35' => 'Jawa Timur',
    '51' => 'Bali',
    // ... complete mapping
]
```

## 📈 Future Enhancements

### Phase 2 Features
- **District Management** - Education district hierarchy
- **School Categories** - State/Private/International classification
- **Facility Management** - Infrastructure and equipment tracking
- **Academic Calendar** - School-specific calendar management

### Phase 3 Features
- **Geolocation Integration** - GPS coordinates for schools
- **School Statistics** - Student/teacher counts, performance metrics
- **Photo Gallery** - School facility images
- **Document Management** - Official school documents

### Integration Planned
- **Dapodik Integration** - Ministry of Education database sync
- **Regional Education Office** - District-level reporting
- **Accreditation Board** - Real-time accreditation status
- **Student Information System** - Student enrollment data

## 🐛 Known Issues

### Current Limitations
- Manual NPSN assignment (no auto-generation)
- No validation for NPSN format compliance
- Limited search and filtering options
- No bulk import/export functionality

### Workarounds
- NPSN format checked manually during creation
- Search by exact match only
- Individual school management required

## 📊 Analytics & Reporting

### School Dashboard Metrics
- Total active schools by province
- Principal assignment status
- User distribution per school
- Accreditation level distribution

### Reports Available
- School directory listing
- Principal contact list
- Regional school distribution
- Inactive/archived schools

---

**Next Module:** [SAW Decision Engine](saw-engine.md)  
**Previous:** [Authentication System](authentication.md)