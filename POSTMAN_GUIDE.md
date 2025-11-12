# 📮 Postman Collection Guide

## 🚀 **Quick Setup**

### **1. Import Collection**
- Open Postman
- Click **Import** → **Upload Files**
- Select `postman_collection.json`

### **2. Set Base URL**
- Collection Variables → `base_url` = `http://localhost:8000/api`

### **3. Authentication Flow**
1. **Login** with admin credentials:
   ```json
   {
     "email": "admin@example.com", 
     "password": "password"
   }
   ```
2. Token auto-saves to `{{auth_token}}` variable
3. All requests use Bearer token automatically

## 📋 **Test Scenarios**

### **🔐 Authentication**
- ✅ Register new user
- ✅ Login (auto-saves token)
- ✅ Get current user profile
- ✅ Logout

### **👥 User Management** (Admin/Manager only)
- ✅ List all users
- ✅ Get user details
- ✅ Update user role

### **📊 Dashboard**
- ✅ Get dashboard statistics
- ✅ Role-based data filtering

### **📁 Projects**
- ✅ Create project
- ✅ List projects (filtered by role)
- ✅ Update/Delete project
- ✅ Add/Remove project members

### **📝 Tasks**
- ✅ Create task with assignees
- ✅ Create task with file attachments
- ✅ List tasks with filters
- ✅ Update task status
- ✅ Assign/Unassign users
- ✅ Search tasks
- ✅ Delete task

### **💬 Comments**
- ✅ Add comment to task
- ✅ List task comments
- ✅ Update/Delete comments

## 🎯 **Default Test Data**

### **Users:**
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Manager | manager@example.com | password |
| Employee | employee1@example.com | password |
| Employee | employee2@example.com | password |

### **Sample Requests:**

**Create Project:**
```json
{
  "name": "Mobile App Development",
  "description": "iOS and Android app project"
}
```

**Create Task:**
```json
{
  "project_id": 1,
  "title": "Design Login Screen",
  "description": "Create wireframes and mockups",
  "priority": "high",
  "due_date": "2024-12-31",
  "assignee_ids": [2, 3]
}
```

**Task Filters:**
- `?status=pending` - Filter by status
- `?priority=high` - Filter by priority  
- `?project_id=1` - Filter by project
- `?search=login` - Search in title/description

## 🔧 **Environment Variables**

| Variable | Value | Description |
|----------|-------|-------------|
| `base_url` | `http://localhost:8000/api` | API base URL |
| `auth_token` | Auto-set on login | Bearer token |

## 📁 **File Upload Testing**

For **"Create Task with Files"** request:
1. Select `form-data` body type
2. Add file in `attachments[]` field
3. Supported: PDF, DOC, DOCX, JPG, PNG (max 2MB)

## 🚀 **Quick Test Flow**

1. **Login** → Get token
2. **Create Project** → Note project ID
3. **Create Task** → Assign to users
4. **Add Comment** → Test collaboration
5. **Update Task Status** → Trigger notifications
6. **Search Tasks** → Test filtering

## 🎯 **Expected Responses**

All responses follow consistent format:
- **Success**: JSON with data + HTTP 200/201
- **Validation Error**: HTTP 422 with error details
- **Unauthorized**: HTTP 401/403 with message
- **Not Found**: HTTP 404 with error message

**Ready to test your enterprise Laravel API!** 🚀