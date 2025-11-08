# 📥 Data Import System Module

**Module:** Data Import & Mapping System  
**Status:** 🚧 In Progress (Phase 2)  
**Dependencies:** Authentication, School Management, File Management  

## Overview

The Data Import System enables schools to upload and process data from external sources including Dapodik (Ministry of Education database), RKAS (School Activity and Budget Plan), and Sarpras (Infrastructure and Facilities) systems. It provides intelligent mapping, validation, and normalization of imported data.

## 🎯 Key Features

- **Multi-Format Support** - CSV, XLSX, JSON file uploads
- **Intelligent Column Mapping** - Auto-detect and manual mapping
- **Data Validation** - Real-time validation with error reporting
- **Preview & Confirmation** - Review data before final import
- **Batch Processing** - Handle large datasets efficiently
- **Import History** - Track all import activities
- **Error Recovery** - Retry failed imports and partial updates

## 📊 Supported Data Sources

### Dapodik (Data Pokok Pendidikan)
- **Student Enrollment** - Jumlah siswa per program keahlian
- **Teacher Qualifications** - Kualifikasi dan sertifikasi guru
- **Curriculum Data** - Program keahlian dan mata pelajaran
- **Academic Achievement** - Nilai ujian dan prestasi siswa

### RKAS (Rencana Kegiatan dan Anggaran Sekolah)
- **Budget Allocation** - Alokasi anggaran per bidang
- **Activity Planning** - Rencana kegiatan sekolah
- **Financial Reports** - Laporan penggunaan dana
- **Cost per Student** - Biaya operasional per siswa

### Sarpras (Sarana dan Prasarana)
- **Infrastructure Condition** - Kondisi bangunan dan fasilitas
- **Equipment Inventory** - Inventaris peralatan pembelajaran
- **Maintenance Records** - Riwayat pemeliharaan fasilitas
- **Utilization Data** - Tingkat pemanfaatan fasilitas

## 🗄️ Database Schema

### Imports Table
```sql
CREATE TABLE imports (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NOT NULL REFERENCES schools(id),
    user_id BIGINT NOT NULL REFERENCES users(id),
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INTEGER NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    source_type ENUM('dapodik','rkas','sarpras','manual') NOT NULL,
    status ENUM('uploaded','mapping','validating','processing','completed','failed') NOT NULL DEFAULT 'uploaded',
    total_rows INTEGER NULL,
    processed_rows INTEGER NULL DEFAULT 0,
    success_rows INTEGER NULL DEFAULT 0,
    error_rows INTEGER NULL DEFAULT 0,
    mapping_config JSONB NULL,
    validation_errors JSONB NULL,
    processing_started_at TIMESTAMP NULL,
    processing_completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Import Mappings Table
```sql
CREATE TABLE import_mappings (
    id BIGSERIAL PRIMARY KEY,
    import_id BIGINT NOT NULL REFERENCES imports(id) ON DELETE CASCADE,
    column_index INTEGER NOT NULL,
    column_name VARCHAR(255) NOT NULL,
    subkriteria_id BIGINT NULL REFERENCES subkriteria(id),
    data_type ENUM('number','text','date','boolean') NOT NULL DEFAULT 'text',
    is_required BOOLEAN NOT NULL DEFAULT false,
    validation_rules JSONB NULL,
    sample_values JSONB NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Import Errors Table
```sql
CREATE TABLE import_errors (
    id BIGSERIAL PRIMARY KEY,
    import_id BIGINT NOT NULL REFERENCES imports(id) ON DELETE CASCADE,
    row_number INTEGER NOT NULL,
    column_name VARCHAR(255) NULL,
    error_type ENUM('validation','mapping','format','duplicate') NOT NULL,
    error_message TEXT NOT NULL,
    raw_value TEXT NULL,
    expected_format VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🚀 API Endpoints

### File Upload

#### Upload File for Import
```http
POST /api/imports/upload
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "file": [file],
    "source_type": "dapodik",
    "description": "Data siswa tahun 2025"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "import_id": 123,
        "filename": "dapodik_2025.xlsx",
        "file_size": 2048576,
        "total_rows": 1500,
        "detected_columns": [
            "nama_siswa",
            "nisn", 
            "program_keahlian",
            "tahun_masuk"
        ],
        "preview_data": [
            ["John Doe", "1234567890", "TKJ", "2023"],
            ["Jane Smith", "0987654321", "MM", "2023"]
        ]
    }
}
```

### Column Mapping

#### Get Available Subkriteria for Mapping
```http
GET /api/imports/{id}/subkriteria
Authorization: Bearer {token}
```

#### Submit Column Mapping
```http
POST /api/imports/{id}/mapping
Authorization: Bearer {token}
Content-Type: application/json

{
    "mappings": [
        {
            "column_index": 0,
            "column_name": "nama_siswa",
            "subkriteria_id": null,
            "data_type": "text"
        },
        {
            "column_index": 1,
            "column_name": "jumlah_siswa",
            "subkriteria_id": 15,
            "data_type": "number"
        }
    ]
}
```

### Data Validation

#### Validate Import Data
```http
POST /api/imports/{id}/validate
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "validation_summary": {
            "total_rows": 1500,
            "valid_rows": 1450,
            "error_rows": 50,
            "warnings": 25
        },
        "errors": [
            {
                "row": 15,
                "column": "jumlah_siswa",
                "message": "Nilai harus berupa angka",
                "value": "tidak ada"
            }
        ],
        "warnings": [
            {
                "row": 23,
                "column": "tahun_masuk",
                "message": "Format tahun tidak standar",
                "value": "23"
            }
        ]
    }
}
```

### Import Processing

#### Execute Import
```http
POST /api/imports/{id}/execute
Authorization: Bearer {token}
Content-Type: application/json

{
    "skip_errors": true,
    "update_existing": false,
    "tahun": 2025
}
```

#### Check Import Status
```http
GET /api/imports/{id}/status
Authorization: Bearer {token}
```

#### Get Import History
```http
GET /api/imports?school_id=1&source_type=dapodik
Authorization: Bearer {token}
```

## 🔍 Data Validation Rules

### Numeric Data
```json
{
    "type": "number",
    "min": 0,
    "max": 9999999,
    "decimal_places": 2,
    "required": true
}
```

### Text Data
```json
{
    "type": "text",
    "min_length": 1,
    "max_length": 255,
    "pattern": "^[A-Za-z0-9\\s]+$",
    "required": false
}
```

### Date Data
```json
{
    "type": "date",
    "format": "YYYY-MM-DD",
    "min_date": "2020-01-01",
    "max_date": "2030-12-31",
    "required": true
}
```

### Boolean Data
```json
{
    "type": "boolean",
    "true_values": ["Ya", "1", "true", "TRUE"],
    "false_values": ["Tidak", "0", "false", "FALSE"],
    "required": false
}
```

## 🧠 Intelligent Mapping

### Auto-Detection Algorithm
1. **Column Name Analysis** - Match known patterns
2. **Data Type Detection** - Analyze sample values
3. **Statistical Analysis** - Range and distribution check
4. **Historical Mapping** - Learn from previous imports
5. **Confidence Score** - Rate mapping accuracy

### Common Mapping Patterns
```javascript
const mappingPatterns = {
    'jumlah_siswa': /^(jml|jumlah).*siswa/i,
    'nama_sekolah': /^(nama|name).*sekolah/i,
    'tahun_ajaran': /^(tahun|year).*(ajaran|academic)/i,
    'anggaran_total': /^(total|jumlah).*(anggaran|budget)/i
};
```

## 📊 File Processing Workflow

### Upload Phase
1. **File Validation** - Size, format, structure check
2. **Virus Scanning** - Security validation
3. **Preview Generation** - First 10 rows display
4. **Column Detection** - Header and data type analysis

### Mapping Phase
1. **Auto-Mapping** - Intelligent column matching
2. **Manual Review** - User confirmation/adjustment
3. **Validation Rules** - Apply data constraints
4. **Preview Mapping** - Show mapped data sample

### Processing Phase
1. **Batch Processing** - Process in chunks
2. **Error Handling** - Skip/retry failed rows
3. **Progress Tracking** - Real-time status updates
4. **Result Summary** - Success/error statistics

### Completion Phase
1. **Data Storage** - Save to nilai_raw table
2. **Trigger Normalization** - Auto-calculate normalized values
3. **Notification** - Inform user of completion
4. **Cleanup** - Archive processed files

## 🔐 Permission Matrix

| Action | Super Admin | Admin | Kepala Sekolah | User |
|--------|-------------|-------|----------------|------|
| Upload files | ✅ | ✅ | ❌ | ❌ |
| Configure mapping | ✅ | ✅ | ❌ | ❌ |
| Execute imports | ✅ | ✅ | ❌ | ❌ |
| View import history | ✅ | ✅ | ✅ | ❌ |
| Download error reports | ✅ | ✅ | ✅ | ❌ |
| Delete import data | ✅ | ❌ | ❌ | ❌ |

## 🧪 Testing & Quality Assurance

### Test Data Samples
```bash
# Generate test CSV files
php artisan import:generate-samples --type=dapodik --rows=100

# Test import validation
php artisan import:test --file=sample_dapodik.csv

# Validate mapping accuracy
php artisan import:validate-mapping --import-id=123
```

### Performance Benchmarks
- **File Upload**: < 30 seconds for 10MB files
- **Validation**: < 60 seconds for 10,000 rows
- **Processing**: < 5 minutes for 50,000 rows
- **Memory Usage**: < 256MB for large imports

## 🔧 Configuration

### Import Settings
```php
// config/import.php
'upload' => [
    'max_file_size' => '50MB',
    'allowed_types' => ['csv', 'xlsx', 'xls'],
    'storage_disk' => 'imports',
    'retention_days' => 90
],

'processing' => [
    'batch_size' => 1000,
    'timeout' => 300,
    'max_errors' => 100,
    'retry_attempts' => 3
],

'validation' => [
    'strict_mode' => false,
    'auto_detect_encoding' => true,
    'skip_empty_rows' => true,
    'trim_whitespace' => true
]
```

### Queue Configuration
```php
// Jobs for background processing
'import_jobs' => [
    'queue' => 'imports',
    'timeout' => 600,
    'retry_after' => 300,
    'max_tries' => 3
]
```

## 📈 Future Enhancements

### Phase 3 Features
- **Real-time API Integration** - Direct Dapodik/RKAS API sync
- **Template Management** - Predefined import templates
- **Scheduled Imports** - Automatic periodic imports
- **Data Transformation** - Custom data processing rules

### Advanced Features
- **Machine Learning Mapping** - AI-powered column detection
- **Multi-sheet Support** - Excel workbook processing
- **Version Control** - Track data changes over time
- **Rollback Capability** - Undo import operations

## 🐛 Known Issues & Limitations

### Current Limitations
- **Single Sheet Support** - Only first sheet processed in Excel
- **Memory Constraints** - Large files may cause timeouts
- **Limited File Formats** - No support for PDF or Word documents
- **Manual Error Review** - No automated error correction

### Workarounds
- Split large files into smaller chunks
- Use CSV format for best compatibility
- Review error reports manually
- Re-upload corrected data files

## 📊 Import Analytics

### Metrics Dashboard
- Import success rates by source type
- Average processing times
- Common error patterns
- User activity statistics

### Reporting Features
- Import history reports
- Error analysis summaries
- Data quality metrics
- Performance trend analysis

---

**Next Module:** [Task Management](task-management.md)  
**Previous:** [SAW Decision Engine](saw-engine.md)