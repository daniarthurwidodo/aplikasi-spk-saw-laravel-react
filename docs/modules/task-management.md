# 🧩 Task Management Module

**Module:** Task & Subtask Workflow System  
**Status:** 📋 Planned (Phase 5)  
**Dependencies:** Authentication, School Management, File Management  

## Overview

The Task Management Module enables schools to convert SAW analysis results into actionable tasks with approval workflows. It provides a Kanban-style interface for tracking progress from planning to completion, with comprehensive approval processes for principals.

## 🎯 Key Features

- **Kanban Board Interface** - Visual task tracking
- **Approval Workflow** - Multi-level task approval system
- **Subtask Management** - Hierarchical task breakdown
- **File Attachments** - Document and evidence management
- **Progress Tracking** - Real-time status updates
- **Automated Reporting** - Task completion reports
- **Deadline Management** - Due date tracking and alerts

## 🔄 Task Workflow States

```
created → in_progress → pending_approval → approved/rejected → done → reported
```

### State Descriptions

| State | Description | Who Can Change | Next States |
|-------|-------------|----------------|-------------|
| **created** | Initial task creation | Creator | in_progress, cancelled |
| **in_progress** | Task being worked on | Assignee | pending_approval, cancelled |
| **pending_approval** | Waiting for principal approval | System | approved, rejected |
| **approved** | Principal has approved task | Kepala Sekolah | done |
| **rejected** | Principal has rejected task | Kepala Sekolah | in_progress |
| **done** | Task completed successfully | Assignee | reported |
| **reported** | Final report generated | System | archived |

## 🗄️ Database Schema

### Tasks Table
```sql
CREATE TABLE tasks (
    id BIGSERIAL PRIMARY KEY,
    school_id BIGINT NOT NULL REFERENCES schools(id),
    created_by BIGINT NOT NULL REFERENCES users(id),
    assigned_to BIGINT NULL REFERENCES users(id),
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
    status ENUM('created','in_progress','pending_approval','approved','rejected','done','reported','cancelled') NOT NULL DEFAULT 'created',
    due_date DATE NULL,
    estimated_hours INTEGER NULL,
    actual_hours INTEGER NULL DEFAULT 0,
    completion_percentage INTEGER NOT NULL DEFAULT 0 CHECK (completion_percentage >= 0 AND completion_percentage <= 100),
    approved_by BIGINT NULL REFERENCES users(id),
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    completed_at TIMESTAMP NULL,
    reported_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Subtasks Table
```sql
CREATE TABLE subtasks (
    id BIGSERIAL PRIMARY KEY,
    task_id BIGINT NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
    assigned_to BIGINT NULL REFERENCES users(id),
    due_date DATE NULL,
    completion_percentage INTEGER NOT NULL DEFAULT 0 CHECK (completion_percentage >= 0 AND completion_percentage <= 100),
    completed_at TIMESTAMP NULL,
    order_index INTEGER NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Task Comments Table
```sql
CREATE TABLE task_comments (
    id BIGSERIAL PRIMARY KEY,
    task_id BIGINT NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id),
    comment TEXT NOT NULL,
    is_internal BOOLEAN NOT NULL DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Task Attachments Table
```sql
CREATE TABLE task_attachments (
    id BIGSERIAL PRIMARY KEY,
    task_id BIGINT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    subtask_id BIGINT NULL REFERENCES subtasks(id) ON DELETE CASCADE,
    upload_id BIGINT NOT NULL REFERENCES uploads(id) ON DELETE CASCADE,
    attachment_type ENUM('document','image','evidence','report') NOT NULL DEFAULT 'document',
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK ((task_id IS NOT NULL AND subtask_id IS NULL) OR (task_id IS NULL AND subtask_id IS NOT NULL))
);
```

## 🚀 API Endpoints

### Task Management

#### List Tasks
```http
GET /api/tasks?school_id=1&status=in_progress&assigned_to=5
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "tasks": [
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
                    "name": "Ahmad Santoso",
                    "role": "user"
                },
                "subtasks_count": 3,
                "attachments_count": 2,
                "created_at": "2025-11-01T10:00:00Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "total_pages": 3,
            "total_items": 25
        }
    }
}
```

#### Create Task
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Enhance Teaching Quality",
    "description": "Implement teacher training program based on SAW recommendations",
    "priority": "high",
    "assigned_to": 8,
    "due_date": "2025-12-31",
    "estimated_hours": 120,
    "subtasks": [
        {
            "title": "Plan training curriculum",
            "description": "Design comprehensive training modules",
            "assigned_to": 8,
            "due_date": "2025-11-30"
        },
        {
            "title": "Schedule training sessions",
            "description": "Coordinate with teachers for availability",
            "assigned_to": 9,
            "due_date": "2025-12-15"
        }
    ]
}
```

#### Update Task Status
```http
PATCH /api/tasks/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "pending_approval",
    "completion_percentage": 90,
    "actual_hours": 85,
    "completion_notes": "All subtasks completed, ready for review"
}
```

#### Approve/Reject Task (Kepala Sekolah only)
```http
POST /api/tasks/{id}/approve
Authorization: Bearer {token}
Content-Type: application/json

{
    "action": "approve",
    "notes": "Excellent work, approved for implementation"
}
```

```http
POST /api/tasks/{id}/reject
Authorization: Bearer {token}
Content-Type: application/json

{
    "action": "reject",
    "reason": "Need more detailed budget breakdown before approval",
    "required_changes": ["Add detailed budget", "Include timeline for implementation"]
}
```

### Subtask Management

#### Create Subtask
```http
POST /api/subtasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "task_id": 1,
    "title": "Purchase lab equipment",
    "description": "Buy microscopes and lab tools",
    "assigned_to": 7,
    "due_date": "2025-11-20"
}
```

#### Update Subtask
```http
PATCH /api/subtasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "completed",
    "completion_percentage": 100,
    "completion_notes": "All equipment purchased and installed"
}
```

### Comments & Communication

#### Add Comment
```http
POST /api/tasks/{id}/comments
Authorization: Bearer {token}
Content-Type: application/json

{
    "comment": "Equipment has been delivered and is being installed",
    "is_internal": false
}
```

#### Get Task Comments
```http
GET /api/tasks/{id}/comments
Authorization: Bearer {token}
```

### File Attachments

#### Upload Task Attachment
```http
POST /api/tasks/{id}/attachments
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "file": [file],
    "attachment_type": "evidence",
    "description": "Photo of installed equipment"
}
```

#### List Task Attachments
```http
GET /api/tasks/{id}/attachments
Authorization: Bearer {token}
```

## 📊 Kanban Board Configuration

### Board Columns
```javascript
const kanbanColumns = [
    {
        id: 'created',
        title: 'To Do',
        status: ['created'],
        color: '#6B7280',
        limit: null
    },
    {
        id: 'in_progress',
        title: 'In Progress',
        status: ['in_progress'],
        color: '#3B82F6',
        limit: 5
    },
    {
        id: 'review',
        title: 'Need Approval',
        status: ['pending_approval'],
        color: '#F59E0B',
        limit: 3
    },
    {
        id: 'done',
        title: 'Done',
        status: ['approved', 'done', 'reported'],
        color: '#10B981',
        limit: null
    }
];
```

### Task Card Display
```javascript
const taskCard = {
    title: task.title,
    assignee: task.assignee.name,
    dueDate: task.due_date,
    priority: task.priority,
    progress: task.completion_percentage,
    subtasksCount: task.subtasks_count,
    attachmentsCount: task.attachments_count,
    commentsCount: task.comments_count,
    tags: task.tags || []
};
```

## 🔐 Permission Matrix

| Action | Super Admin | Admin | Kepala Sekolah | User |
|--------|-------------|-------|----------------|------|
| Create tasks | ✅ | ✅ | ✅ | ✅ |
| View all school tasks | ✅ | ✅ | ✅ | ❌ |
| View assigned tasks | ✅ | ✅ | ✅ | ✅ |
| Update task status | ✅ | ✅ | ✅ | ✅ (own tasks) |
| Approve/reject tasks | ✅ | ❌ | ✅ | ❌ |
| Delete tasks | ✅ | ✅ | ✅ | ❌ |
| Assign tasks to others | ✅ | ✅ | ✅ | ❌ |
| View reports | ✅ | ✅ | ✅ | ❌ |

## 🔔 Notification System

### Notification Triggers
- **Task Assignment** - Notify assignee of new task
- **Status Change** - Notify stakeholders of progress updates
- **Approval Required** - Alert principal when approval needed
- **Due Date Approaching** - Warn about upcoming deadlines
- **Task Completed** - Inform creator of task completion
- **Rejection** - Notify assignee of task rejection

### Notification Channels
- **In-App Notifications** - Real-time dashboard alerts
- **Email Notifications** - Detailed email updates
- **Push Notifications** - Mobile app alerts (future)
- **WhatsApp Integration** - SMS notifications (planned)

## 📈 Progress Tracking & Analytics

### Task Metrics
```javascript
const taskMetrics = {
    totalTasks: 45,
    completedTasks: 32,
    overdueTasks: 3,
    averageCompletionTime: 8.5, // days
    completionRate: 71.1, // percentage
    tasksByPriority: {
        urgent: 2,
        high: 8,
        medium: 25,
        low: 10
    },
    tasksByStatus: {
        created: 5,
        in_progress: 8,
        pending_approval: 3,
        approved: 2,
        done: 27
    }
};
```

### Performance Reports
- **Individual Performance** - Tasks completed per user
- **Team Performance** - Department/role-based statistics
- **Time Analysis** - Average completion times by task type
- **Bottleneck Analysis** - Identify approval delays
- **Trend Analysis** - Month-over-month progress tracking

## 🧪 Testing & Quality Assurance

### Test Scenarios
```bash
# Test task workflow
php artisan task:test-workflow --task-id=123

# Generate test data
php artisan task:generate-samples --count=50

# Test approval process
php artisan task:test-approvals --principal-id=5
```

### Performance Benchmarks
- **Task Creation**: < 1 second response time
- **Status Updates**: < 500ms response time
- **File Upload**: < 10 seconds for 5MB files
- **Report Generation**: < 30 seconds for 100 tasks

## 🔧 Configuration

### Task Management Settings
```php
// config/tasks.php
'workflow' => [
    'auto_approval_threshold' => 80, // Auto-approve at 80% completion
    'approval_timeout_days' => 7,
    'overdue_notification_days' => [1, 3, 7],
    'max_attachments_per_task' => 10
],

'notifications' => [
    'email_enabled' => true,
    'push_enabled' => false,
    'digest_frequency' => 'daily',
    'quiet_hours' => ['22:00', '06:00']
],

'kanban' => [
    'column_limits' => [
        'in_progress' => 5,
        'pending_approval' => 3
    ],
    'auto_archive_days' => 90,
    'priority_colors' => [
        'urgent' => '#EF4444',
        'high' => '#F97316',
        'medium' => '#EAB308',
        'low' => '#22C55E'
    ]
]
```

## 📋 Task Templates

### Pre-defined Templates
```json
{
    "infrastructure_improvement": {
        "title": "Infrastructure Improvement Plan",
        "description": "Systematic approach to upgrading school facilities",
        "priority": "high",
        "estimated_hours": 160,
        "subtasks": [
            "Conduct facility assessment",
            "Prepare improvement budget",
            "Source suppliers and contractors",
            "Execute improvement plan",
            "Verify completion and quality"
        ]
    },
    "teacher_development": {
        "title": "Teacher Professional Development",
        "description": "Enhance teaching capabilities and qualifications",
        "priority": "medium",
        "estimated_hours": 80,
        "subtasks": [
            "Identify training needs",
            "Design training program",
            "Schedule training sessions",
            "Conduct training",
            "Evaluate training effectiveness"
        ]
    }
}
```

## 🚀 Future Enhancements

### Phase 6 Features
- **Gantt Chart View** - Timeline visualization
- **Resource Management** - Budget and resource allocation
- **Integration with SAW** - Auto-generate tasks from analysis
- **Mobile Application** - On-the-go task management

### Advanced Features
- **AI Task Suggestions** - Machine learning task recommendations
- **Automated Workflows** - Rule-based task automation
- **Integration APIs** - Connect with external project management tools
- **Custom Fields** - School-specific task attributes

## 🐛 Known Limitations

### Current Constraints
- Single assignee per task (no team assignments)
- Limited file format support for attachments
- No time tracking integration
- Manual task prioritization only

### Planned Improvements
- Multi-user task assignment
- Advanced file preview capabilities
- Built-in time tracking
- Auto-priority based on SAW scores

---

**Next Module:** [File Management](file-management.md)  
**Previous:** [Data Import System](data-import.md)