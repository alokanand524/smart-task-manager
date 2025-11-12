# Smart Task Management System

A comprehensive task management system built with Laravel 10, featuring role-based access control, real-time notifications, and activity tracking.

## 🚀 Features

### Core Functionality
- **User Authentication** - Laravel Sanctum JWT-based authentication
- **Role Management** - Admin, Manager, Employee roles with different permissions
- **Project Management** - Create, update, delete projects with team collaboration
- **Task Management** - Full CRUD operations with status tracking and assignments
- **Comment System** - Task-based commenting with real-time updates
- **Activity Logging** - Redis-powered activity tracking for audit trails
- **Email Notifications** - Queue-based email notifications for task updates

### Technical Features
- **RESTful API Design** - Clean, well-structured API endpoints
- **Role-Based Access Control** - Middleware and policies for secure access
- **Queue Jobs** - Background processing for email notifications
- **Redis Caching** - Fast activity logging and session management
- **API Documentation** - Swagger/OpenAPI documentation
- **Database Relationships** - Optimized Eloquent relationships
- **Input Validation** - Comprehensive request validation

## 🛠️ Tech Stack

- **Backend**: Laravel 10
- **Database**: PostgreSQL
- **Cache/Queue**: Redis
- **Authentication**: Laravel Sanctum
- **Email**: Queue-based notifications
- **Documentation**: Swagger/OpenAPI
- **Testing**: PHPUnit

## 📋 API Endpoints

### Authentication
```
POST /api/register     - Register new user
POST /api/login        - User login
GET  /api/user         - Get current user
POST /api/logout       - User logout
```

### Dashboard
```
GET  /api/dashboard    - Get dashboard statistics
```

### Users (Admin/Manager only)
```
GET  /api/users        - List all users
GET  /api/users/{id}   - Get user details
PUT  /api/users/{id}/role - Update user role
```

### Projects
```
GET    /api/projects           - List projects
POST   /api/projects           - Create project
GET    /api/projects/{id}      - Get project details
PUT    /api/projects/{id}      - Update project
DELETE /api/projects/{id}      - Delete project
POST   /api/projects/{id}/members    - Add member
DELETE /api/projects/{id}/members    - Remove member
```

### Tasks
```
GET    /api/tasks              - List tasks
POST   /api/tasks              - Create task
GET    /api/tasks/{id}         - Get task details
PUT    /api/tasks/{id}         - Update task
DELETE /api/tasks/{id}         - Delete task
POST   /api/tasks/{id}/assign  - Assign user to task
DELETE /api/tasks/{id}/assign  - Unassign user from task
```

### Comments
```
GET    /api/tasks/{id}/comments - Get task comments
POST   /api/tasks/{id}/comments - Add comment
PUT    /api/comments/{id}       - Update comment
DELETE /api/comments/{id}       - Delete comment
```

## 🔧 Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd smart-task-manager
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** (Update .env file)
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_task_manager_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. **Configure Redis** (Update .env file)
```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Generate API documentation**
```bash
php artisan l5-swagger:generate
```

8. **Start the application**
```bash
php artisan serve
php artisan queue:work  # In separate terminal for queue processing
```

## 👥 Default Users

The system comes with pre-seeded users for testing:

| Role     | Email                | Password |
|----------|---------------------|----------|
| Admin    | admin@example.com   | password |
| Manager  | manager@example.com | password |
| Employee | employee1@example.com | password |
| Employee | employee2@example.com | password |

## 🔐 Role Permissions

### Admin
- Full system access
- Manage all users, projects, and tasks
- Delete any content
- Access all analytics

### Manager
- Create and manage projects
- Assign tasks to team members
- View team analytics
- Manage project members

### Employee
- View assigned projects and tasks
- Update task status
- Add comments to tasks
- View personal dashboard

## 📊 Dashboard Analytics

The dashboard provides role-based analytics:

- **Total Projects/Tasks** - Count based on user access
- **Task Distribution** - Status-wise task breakdown
- **Overdue Tasks** - Tasks past due date
- **Recent Activities** - Latest system activities (Admin/Manager)
- **Personal Tasks** - User-specific task summary

## 🔄 Activity Logging

All major actions are logged to Redis for fast retrieval:
- User registration/login
- Task creation/updates
- Project modifications
- Status changes

## 📧 Email Notifications

Queue-based email notifications for:
- Task assignments
- Status updates
- Project invitations
- Due date reminders

## 📖 API Documentation

Access Swagger documentation at: `http://localhost:8000/api/documentation`

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 🚀 Deployment

For production deployment:

1. **Environment Configuration**
```env
APP_ENV=production
APP_DEBUG=false
```

2. **Optimize Application**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

3. **Queue Worker Setup**
```bash
# Setup supervisor or similar process manager for queue workers
php artisan queue:work --daemon
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🔗 Links

- **API Documentation**: `/api/documentation`
- **Health Check**: `/api/health` (if implemented)
- **Postman Collection**: Available in `/docs` folder

---

**Built with ❤️ using Laravel 10**