# ⚖️ SAW Decision Engine Module

**Module:** Simple Additive Weighting (SAW) Decision Support System  
**Status:** 📋 Planned (Phase 3)  
**Dependencies:** Authentication, School Management, Data Import  

## Overview

The SAW Decision Engine is the core analytical component of SPK-SAWh that implements the Simple Additive Weighting method for multi-criteria decision analysis. It processes school performance data across multiple criteria to generate rankings and recommendations.

## 🎯 Key Features

- **Multi-Criteria Analysis** - Evaluate schools across 7 main bidang (A-G)
- **Automated Normalization** - Convert raw data to comparable scales
- **Weighted Scoring** - Apply importance weights to different criteria
- **Ranking Generation** - Produce ordered lists of alternatives
- **Trend Analysis** - Track performance over time
- **Visual Dashboard** - Radar charts and performance indicators

## 📊 SAW Methodology

### Algorithm Steps

1. **Data Collection** - Import raw values from Dapodik, RKAS, Sarpras
2. **Normalization** - Convert to 0-1 scale using min-max or z-score
3. **Weight Application** - Multiply normalized values by criteria weights
4. **Score Calculation** - Sum weighted values for each alternative
5. **Ranking** - Sort alternatives by final SAW scores

### Mathematical Formula

```
SAW Score = Σ(Wi × Rij)

Where:
- Wi = Weight of criteria i
- Rij = Normalized value of alternative j for criteria i
- Σ = Sum across all criteria
```

## 🗄️ Database Schema

### Kriteria (Criteria) Table
```sql
CREATE TABLE kriteria (
    id BIGSERIAL PRIMARY KEY,
    kode VARCHAR(10) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    deskripsi TEXT NULL,
    bidang ENUM('A','B','C','D','E','F','G') NOT NULL,
    tipe ENUM('benefit','cost') NOT NULL DEFAULT 'benefit',
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Subkriteria Table
```sql
CREATE TABLE subkriteria (
    id BIGSERIAL PRIMARY KEY,
    kriteria_id BIGINT NOT NULL REFERENCES kriteria(id) ON DELETE CASCADE,
    kode VARCHAR(20) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    satuan VARCHAR(50) NULL,
    target_min DECIMAL(15,2) NULL,
    target_max DECIMAL(15,2) NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Bobot (Weights) Table
```sql
CREATE TABLE bobot (
    id BIGSERIAL PRIMARY KEY,
    kriteria_id BIGINT NOT NULL REFERENCES kriteria(id),
    school_id BIGINT NULL REFERENCES schools(id),
    nilai DECIMAL(5,4) NOT NULL CHECK (nilai >= 0 AND nilai <= 1),
    tahun INTEGER NOT NULL,
    status ENUM('draft','approved','archived') NOT NULL DEFAULT 'draft',
    approved_by BIGINT NULL REFERENCES users(id),
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(kriteria_id, school_id, tahun, status)
);
```

### Nilai Raw (Raw Values) Table
```sql
CREATE TABLE nilai_raw (
    id BIGSERIAL PRIMARY KEY,
    subkriteria_id BIGINT NOT NULL REFERENCES subkriteria(id),
    school_id BIGINT NOT NULL REFERENCES schools(id),
    nilai DECIMAL(15,2) NOT NULL,
    tahun INTEGER NOT NULL,
    sumber ENUM('dapodik','rkas','sarpras','manual') NOT NULL,
    import_id BIGINT NULL REFERENCES imports(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(subkriteria_id, school_id, tahun)
);
```

### Normalisasi (Normalized Values) Table
```sql
CREATE TABLE normalisasi (
    id BIGSERIAL PRIMARY KEY,
    nilai_raw_id BIGINT NOT NULL REFERENCES nilai_raw(id),
    nilai_normal DECIMAL(10,8) NOT NULL CHECK (nilai_normal >= 0 AND nilai_normal <= 1),
    metode ENUM('min_max','z_score','vector') NOT NULL DEFAULT 'min_max',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Hasil SAW (SAW Results) Table
```sql
CREATE TABLE hasil_saw (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NOT NULL REFERENCES schools(id),
    tahun INTEGER NOT NULL,
    skor_total DECIMAL(10,6) NOT NULL,
    ranking INTEGER NOT NULL,
    skor_per_bidang JSONB NULL, -- {"A": 0.85, "B": 0.72, ...}
    computation_id UUID NOT NULL,
    computed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(school_id, tahun, computation_id)
);
```

## 🏗️ Bidang Classification (A-G)

### Educational Performance Areas

| Bidang | Nama | Deskripsi | Contoh Kriteria |
|--------|------|-----------|-----------------|
| **A** | Manajemen & Tata Kelola | School management and governance | Leadership quality, strategic planning |
| **B** | Kurikulum & Pembelajaran | Curriculum and learning processes | Teaching methods, student achievement |
| **C** | SDM Guru & Tendik | Human resources (teachers and staff) | Teacher qualifications, training hours |
| **D** | Sarana & Prasarana | Facilities and infrastructure | Building condition, equipment availability |
| **E** | Pembiayaan & Keuangan | Financing and financial management | Budget allocation, financial transparency |
| **F** | Kesiswaan | Student affairs and development | Student activities, counseling services |
| **G** | Kemitraan Industri | Industry partnerships | Internship programs, job placement rates |

## 🚀 API Endpoints (Planned)

### Criteria Management

#### List Criteria
```http
GET /api/kriteria
Authorization: Bearer {token}
```

#### Create Criteria
```http
POST /api/kriteria
Authorization: Bearer {token}
Content-Type: application/json

{
    "kode": "A1",
    "nama": "Kualitas Kepemimpinan",
    "deskripsi": "Penilaian terhadap kualitas kepemimpinan kepala sekolah",
    "bidang": "A",
    "tipe": "benefit"
}
```

### Weight Management

#### Get Current Weights
```http
GET /api/bobot?tahun=2025&school_id=1
Authorization: Bearer {token}
```

#### Submit Weight Proposal
```http
POST /api/bobot
Authorization: Bearer {token}
Content-Type: application/json

{
    "tahun": 2025,
    "school_id": 1,
    "weights": [
        {"kriteria_id": 1, "nilai": 0.25},
        {"kriteria_id": 2, "nilai": 0.20},
        {"kriteria_id": 3, "nilai": 0.15}
    ]
}
```

#### Approve Weights (Super Admin)
```http
POST /api/bobot/{id}/approve
Authorization: Bearer {token}
```

### SAW Computation

#### Trigger SAW Calculation
```http
POST /api/saw/compute
Authorization: Bearer {token}
Content-Type: application/json

{
    "tahun": 2025,
    "school_ids": [1, 2, 3, 4, 5],
    "force_recalculate": false
}
```

#### Get SAW Results
```http
GET /api/saw/results?tahun=2025&school_id=1
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
        "comparison": {
            "total_schools": 5,
            "percentile": 80,
            "above_average": true
        }
    }
}
```

#### Get Regional Rankings
```http
GET /api/saw/rankings?tahun=2025&province=Aceh
Authorization: Bearer {token}
```

## 🧮 Normalization Methods

### Min-Max Normalization (Default)
```
R_ij = (X_ij - Min_j) / (Max_j - Min_j)  [for benefit criteria]
R_ij = (Max_j - X_ij) / (Max_j - Min_j)  [for cost criteria]
```

### Z-Score Normalization
```
R_ij = (X_ij - Mean_j) / StdDev_j
```

### Vector Normalization
```
R_ij = X_ij / √(Σ X_ij²)
```

## 📊 Weight Assignment Strategy

### Default Weight Distribution
```json
{
    "A": 0.20,  // Manajemen & Tata Kelola
    "B": 0.18,  // Kurikulum & Pembelajaran  
    "C": 0.15,  // SDM Guru & Tendik
    "D": 0.12,  // Sarana & Prasarana
    "E": 0.10,  // Pembiayaan & Keuangan
    "F": 0.13,  // Kesiswaan
    "G": 0.12   // Kemitraan Industri
}
```

### Custom Weight Rules
- **Sum Constraint**: All weights must sum to 1.0
- **Range Validation**: Each weight between 0.05 and 0.50
- **Approval Required**: Changes need Super Admin approval
- **Historical Tracking**: All weight changes logged

## 🎨 Dashboard Visualizations

### Radar Chart Configuration
```javascript
const radarConfig = {
    data: {
        labels: ['Manajemen', 'Kurikulum', 'SDM', 'Sarpras', 'Keuangan', 'Kesiswaan', 'Kemitraan'],
        datasets: [{
            label: school.name,
            data: [0.85, 0.72, 0.68, 0.91, 0.75, 0.78, 0.82],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)'
        }]
    },
    options: {
        scales: {
            r: {
                min: 0,
                max: 1,
                ticks: { stepSize: 0.2 }
            }
        }
    }
};
```

### Performance Indicators
- **Color Coding**: Green (>0.8), Yellow (0.6-0.8), Red (<0.6)
- **Trend Arrows**: ↗️ Improving, ➡️ Stable, ↘️ Declining
- **Priority Flags**: 🔴 Needs Attention, 🟡 Monitor, 🟢 Good

## 🔐 Permission Matrix

| Action | Super Admin | Admin | Kepala Sekolah | User |
|--------|-------------|-------|----------------|------|
| Manage criteria | ✅ | ❌ | ❌ | ❌ |
| Propose weights | ✅ | ✅ | ✅ | ❌ |
| Approve weights | ✅ | ❌ | ❌ | ❌ |
| Trigger computation | ✅ | ✅ | ❌ | ❌ |
| View own school results | ✅ | ✅ | ✅ | ✅ |
| View all school results | ✅ | ❌ | ❌ | ❌ |
| Export reports | ✅ | ✅ | ✅ | ❌ |

## 🧪 Testing & Validation

### Algorithm Validation
```bash
# Test SAW computation with sample data
php artisan saw:test --sample-data

# Validate normalization methods
php artisan saw:validate-normalization

# Check weight constraints
php artisan saw:validate-weights
```

### Performance Benchmarks
- **Computation Time**: < 3 seconds per school
- **Memory Usage**: < 512MB for 100 schools
- **Accuracy**: ±0.001 precision for scores

## 🔧 Configuration

### SAW Engine Settings
```php
// config/saw.php
'normalization' => [
    'default_method' => 'min_max',
    'precision' => 8,
    'cache_ttl' => 3600  // 1 hour
],

'weights' => [
    'require_approval' => true,
    'sum_tolerance' => 0.001,
    'min_weight' => 0.05,
    'max_weight' => 0.50
],

'computation' => [
    'batch_size' => 50,
    'timeout' => 300,  // 5 minutes
    'auto_archive' => true
]
```

## 📈 Future Enhancements

### Phase 4 Features
- **Multi-Year Trend Analysis** - Performance tracking over time
- **Predictive Analytics** - Forecast future performance
- **Sensitivity Analysis** - Impact of weight changes
- **Benchmarking** - Compare with national averages

### Advanced Features
- **Machine Learning Integration** - Auto-weight optimization
- **Real-time Computation** - Live dashboard updates
- **Custom Criteria** - School-specific evaluation criteria
- **Export Formats** - PDF, Excel, PowerPoint reports

## 🐛 Known Limitations

### Current Constraints
- Single normalization method per computation
- Manual weight assignment only
- No real-time data updates
- Limited to 7 main bidang

### Performance Considerations
- Large datasets may require batch processing
- Complex calculations need background jobs
- Database optimization required for scale

---

**Next Module:** [Data Import System](data-import.md)  
**Previous:** [School Management](school-management.md)