# 📎 File Management Module

**Module:** Upload & Storage System  
**Status:** 📋 Planned (Phase 5)  
**Dependencies:** Authentication, MinIO/S3 Storage  

## Overview

The File Management Module provides secure file upload, storage, and access control for the SPK-SAWh system. It supports multiple file types, generates thumbnails, and integrates with other modules for document management and evidence storage.

## 🎯 Key Features

- **Multi-Format Support** - Images, PDFs, Office documents, spreadsheets
- **Secure Storage** - S3-compatible object storage with encryption
- **Access Control** - Role-based file access permissions
- **Thumbnail Generation** - Automatic image and PDF thumbnails
- **File Versioning** - Track file changes and history
- **Metadata Management** - Rich file metadata and tagging
- **Virus Scanning** - Built-in malware detection
- **Bulk Operations** - Multi-file upload and management

## 🗄️ Database Schema

### Uploads Table
```sql
CREATE TABLE uploads (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id),
    school_id BIGINT NOT NULL REFERENCES schools(id),
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_hash VARCHAR(64) NOT NULL,
    storage_disk VARCHAR(50) NOT NULL DEFAULT 's3',
    is_public BOOLEAN NOT NULL DEFAULT false,
    is_temporary BOOLEAN NOT NULL DEFAULT false,
    expires_at TIMESTAMP NULL,
    related_table VARCHAR(100) NULL,
    related_id BIGINT NULL,
    metadata JSONB NULL,
    virus_scan_status ENUM('pending','clean','infected','error') NULL,
    virus_scan_result JSONB NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

### File Thumbnails Table
```sql
CREATE TABLE file_thumbnails (
    id BIGSERIAL PRIMARY KEY,
    upload_id BIGINT NOT NULL REFERENCES uploads(id) ON DELETE CASCADE,
    size ENUM('small','medium','large') NOT NULL,
    width INTEGER NOT NULL,
    height INTEGER NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### File Access Logs Table
```sql
CREATE TABLE file_access_logs (
    id BIGSERIAL PRIMARY KEY,
    upload_id BIGINT NOT NULL REFERENCES uploads(id),
    user_id BIGINT NULL REFERENCES users(id),
    action ENUM('view','download','delete','share') NOT NULL,
    ip_address INET NULL,
    user_agent TEXT NULL,
    accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### File Shares Table
```sql
CREATE TABLE file_shares (
    id BIGSERIAL PRIMARY KEY,
    upload_id BIGINT NOT NULL REFERENCES uploads(id) ON DELETE CASCADE,
    shared_by BIGINT NOT NULL REFERENCES users(id),
    shared_with BIGINT NULL REFERENCES users(id),
    share_token VARCHAR(64) UNIQUE NOT NULL,
    permissions JSONB NOT NULL DEFAULT '{"view": true, "download": false}',
    expires_at TIMESTAMP NULL,
    access_count INTEGER NOT NULL DEFAULT 0,
    max_access_count INTEGER NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🚀 API Endpoints

### File Upload

#### Upload Single File
```http
POST /api/uploads
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "file": [file],
    "related_table": "tasks",
    "related_id": 123,
    "is_public": false,
    "generate_thumbnails": true,
    "description": "Project documentation"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 456,
        "filename": "project_docs_20251108_143022.pdf",
        "original_filename": "Project Documentation.pdf",
        "file_size": 2048576,
        "mime_type": "application/pdf",
        "file_hash": "a1b2c3d4e5f6...",
        "download_url": "/api/uploads/456/download",
        "preview_url": "/api/uploads/456/preview",
        "thumbnails": [
            {
                "size": "small",
                "url": "/api/uploads/456/thumbnail/small"
            }
        ],
        "metadata": {
            "pages": 15,
            "created_with": "Microsoft Word",
            "author": "John Doe"
        }
    }
}
```

#### Upload Multiple Files
```http
POST /api/uploads/bulk
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "files[]": [file1, file2, file3],
    "related_table": "imports",
    "related_id": 789
}
```

### File Access

#### Download File
```http
GET /api/uploads/{id}/download
Authorization: Bearer {token}
```

#### Preview File (Images/PDFs)
```http
GET /api/uploads/{id}/preview
Authorization: Bearer {token}
```

#### Get File Thumbnail
```http
GET /api/uploads/{id}/thumbnail/{size}
Authorization: Bearer {token}
```

#### Get File Information
```http
GET /api/uploads/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 456,
        "filename": "project_docs_20251108_143022.pdf",
        "original_filename": "Project Documentation.pdf",
        "file_size": 2048576,
        "mime_type": "application/pdf",
        "uploaded_by": {
            "id": 5,
            "name": "Ahmad Santoso"
        },
        "school": {
            "id": 1,
            "name": "SMK Negeri 1 Banda Aceh"
        },
        "related_to": {
            "table": "tasks",
            "id": 123,
            "title": "Infrastructure Improvement"
        },
        "metadata": {
            "pages": 15,
            "file_version": "1.4",
            "created_with": "Microsoft Word"
        },
        "virus_scan": {
            "status": "clean",
            "scanned_at": "2025-11-08T14:30:22Z"
        },
        "access_stats": {
            "view_count": 12,
            "download_count": 3,
            "last_accessed": "2025-11-08T16:45:00Z"
        },
        "created_at": "2025-11-08T14:30:22Z"
    }
}
```

### File Management

#### List Files
```http
GET /api/uploads?school_id=1&related_table=tasks&mime_type=application/pdf
Authorization: Bearer {token}
```

#### Update File Metadata
```http
PATCH /api/uploads/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "description": "Updated project documentation",
    "tags": ["project", "documentation", "final"],
    "is_public": false
}
```

#### Delete File
```http
DELETE /api/uploads/{id}
Authorization: Bearer {token}
```

### File Sharing

#### Create Share Link
```http
POST /api/uploads/{id}/share
Authorization: Bearer {token}
Content-Type: application/json

{
    "expires_at": "2025-12-31T23:59:59Z",
    "max_access_count": 10,
    "permissions": {
        "view": true,
        "download": true,
        "comment": false
    },
    "password": "optional_password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "share_token": "abc123def456ghi789",
        "share_url": "https://spksaw.app/shared/abc123def456ghi789",
        "expires_at": "2025-12-31T23:59:59Z",
        "permissions": {
            "view": true,
            "download": true
        }
    }
}
```

#### Access Shared File
```http
GET /api/shared/{share_token}
```

## 📁 Supported File Types

### Documents
- **PDF** - Portable Document Format
- **DOC/DOCX** - Microsoft Word documents
- **XLS/XLSX** - Microsoft Excel spreadsheets
- **PPT/PPTX** - Microsoft PowerPoint presentations
- **TXT** - Plain text files
- **RTF** - Rich Text Format

### Images
- **JPEG/JPG** - JPEG images
- **PNG** - Portable Network Graphics
- **GIF** - Graphics Interchange Format
- **WEBP** - WebP images
- **BMP** - Bitmap images
- **SVG** - Scalable Vector Graphics

### Archives
- **ZIP** - ZIP archives
- **RAR** - RAR archives
- **7Z** - 7-Zip archives
- **TAR** - TAR archives

### Media (Limited Support)
- **MP4** - Video files (metadata only)
- **MP3** - Audio files (metadata only)

## 🔐 Security Features

### Access Control
```javascript
const filePermissions = {
    'super_admin': ['view', 'download', 'delete', 'share', 'admin'],
    'admin': ['view', 'download', 'delete', 'share'],
    'kepala_sekolah': ['view', 'download', 'share'],
    'user': ['view', 'download']
};
```

### File Validation
- **Size Limits** - Configurable per file type
- **MIME Type Validation** - Strict content type checking
- **File Extension Validation** - Whitelist approach
- **Content Scanning** - Detect malicious content
- **Hash Verification** - Prevent file corruption

### Virus Scanning
```javascript
const virusScanConfig = {
    enabled: true,
    quarantine_infected: true,
    scan_on_upload: true,
    scan_scheduler: 'daily',
    engines: ['clamav', 'defender'],
    max_file_size: '100MB'
};
```

## 🖼️ Thumbnail Generation

### Image Thumbnails
```javascript
const thumbnailSizes = {
    small: { width: 150, height: 150 },
    medium: { width: 300, height: 300 },
    large: { width: 600, height: 600 }
};
```

### PDF Thumbnails
- **First Page Preview** - Generate from first page
- **Multi-Page Contact Sheet** - Grid of multiple pages
- **Vector Preservation** - Maintain quality for text

### Office Document Thumbnails
- **Cover Page Preview** - Generate from first slide/page
- **Document Icon** - Fallback icons for unsupported formats

## 💾 Storage Configuration

### S3-Compatible Storage (MinIO)
```php
// config/filesystems.php
'uploads' => [
    'driver' => 's3',
    'key' => env('MINIO_ACCESS_KEY'),
    'secret' => env('MINIO_SECRET_KEY'),
    'region' => 'us-east-1',
    'bucket' => env('MINIO_BUCKET', 'spksaw-uploads'),
    'url' => env('MINIO_URL'),
    'endpoint' => env('MINIO_ENDPOINT'),
    'use_path_style_endpoint' => true,
    'throw' => false,
],

'thumbnails' => [
    'driver' => 's3',
    'key' => env('MINIO_ACCESS_KEY'),
    'secret' => env('MINIO_SECRET_KEY'),
    'bucket' => env('MINIO_BUCKET', 'spksaw-thumbnails'),
    'endpoint' => env('MINIO_ENDPOINT'),
]
```

### Local Storage (Development)
```php
'local_uploads' => [
    'driver' => 'local',
    'root' => storage_path('app/uploads'),
    'url' => env('APP_URL').'/storage/uploads',
    'visibility' => 'private',
]
```

## 🔐 Permission Matrix

| Action | Super Admin | Admin | Kepala Sekolah | User |
|--------|-------------|-------|----------------|------|
| Upload files | ✅ | ✅ | ✅ | ✅ |
| View own files | ✅ | ✅ | ✅ | ✅ |
| View school files | ✅ | ✅ | ✅ | ❌ |
| Download files | ✅ | ✅ | ✅ | ✅ |
| Delete own files | ✅ | ✅ | ✅ | ✅ |
| Delete any files | ✅ | ✅ | ❌ | ❌ |
| Share files | ✅ | ✅ | ✅ | ✅ |
| Access file analytics | ✅ | ✅ | ✅ | ❌ |

## 📊 File Analytics

### Usage Statistics
```javascript
const fileAnalytics = {
    totalFiles: 1250,
    totalSize: '15.2 GB',
    storageUsed: 68.4, // percentage
    filesByType: {
        'application/pdf': 450,
        'image/jpeg': 320,
        'application/vnd.ms-excel': 180,
        'image/png': 150,
        'application/msword': 150
    },
    uploadTrend: {
        thisMonth: 145,
        lastMonth: 98,
        growth: 47.9 // percentage
    },
    topUploaders: [
        { user: 'Ahmad Santoso', count: 45 },
        { user: 'Siti Nurhaliza', count: 38 }
    ]
};
```

### Access Reports
- **Most Downloaded Files** - Popular content tracking
- **User Activity** - Upload and download patterns
- **Storage Usage** - School-wise storage consumption
- **File Type Distribution** - Content type analysis

## 🧪 Testing

### Upload Testing
```bash
# Test file upload
php artisan upload:test --file=sample.pdf --user-id=5

# Test thumbnail generation
php artisan upload:generate-thumbnails --upload-id=123

# Test virus scanning
php artisan upload:scan-virus --upload-id=456
```

### Performance Testing
- **Upload Speed** - Test with various file sizes
- **Concurrent Uploads** - Multiple user upload testing
- **Storage Performance** - Read/write speed benchmarks
- **Thumbnail Generation** - Processing time analysis

## 🔧 Configuration

### Upload Settings
```php
// config/uploads.php
'upload' => [
    'max_file_size' => '50MB',
    'max_files_per_upload' => 10,
    'allowed_extensions' => [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        'archives' => ['zip', 'rar', '7z'],
        'text' => ['txt', 'csv', 'rtf']
    ],
    'forbidden_extensions' => ['exe', 'bat', 'cmd', 'scr'],
    'quarantine_path' => 'quarantine/',
    'temp_path' => 'temp/',
    'cleanup_temp_hours' => 24
],

'thumbnails' => [
    'generate_on_upload' => true,
    'sizes' => ['small', 'medium', 'large'],
    'quality' => 85,
    'format' => 'webp',
    'background_processing' => true
],

'virus_scan' => [
    'enabled' => env('VIRUS_SCAN_ENABLED', true),
    'engine' => 'clamav',
    'quarantine_infected' => true,
    'scan_timeout' => 300
]
```

## 🚀 Future Enhancements

### Phase 6 Features
- **Advanced File Search** - Full-text search in documents
- **Version Control** - File versioning and history
- **Collaborative Editing** - Real-time document collaboration
- **Advanced Preview** - In-browser document preview

### Integration Features
- **Office 365 Integration** - Direct Office document editing
- **Google Drive Sync** - Cloud storage synchronization
- **Dropbox Integration** - External storage connectivity
- **Email Attachments** - Direct email file sharing

## 🐛 Known Limitations

### Current Constraints
- **Single Storage Backend** - No multi-cloud support
- **Limited Preview Support** - Basic file type previews only
- **No File Versioning** - Single version per file
- **Basic Search** - Filename and metadata search only

### Planned Improvements
- **Multi-cloud Storage** - Support multiple storage providers
- **Enhanced Preview** - Advanced document preview capabilities
- **Full-text Search** - Search within document content
- **Advanced Metadata** - Rich metadata extraction

---

**Next Module:** [API Reference](../api-reference.md)  
**Previous:** [Task Management](task-management.md)