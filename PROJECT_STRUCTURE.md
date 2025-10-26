# DailyDo - Project Structure

## Clean PHP/MySQL Project Structure

```
To-Do-List/
├── api/                    # Backend API endpoints
│   ├── admin.php          # Admin management API
│   ├── login.php          # User login API
│   ├── logout.php         # User logout API
│   ├── profile-clean.php  # Profile data API (clean version)
│   ├── profile.php        # Profile update API
│   ├── register.php       # User registration API
│   ├── tasks.php          # Tasks CRUD API
│   └── user.php           # User data API
│
├── assets/                 # Frontend assets
│   ├── images/            # Image files
│   │   └── To-Do_BG.png
│   ├── script.js          # JavaScript functionality
│   └── style.css          # CSS styles
│
├── config/                 # Configuration files
│   └── database.php       # Database connection config
│
├── includes/               # PHP includes/helpers
│   └── auth.php           # Authentication functions
│
├── admin.php               # Admin dashboard page
├── calendar.php            # Calendar view page
├── dashboard.php           # User dashboard page
├── index.php               # Landing page
├── login.php               # Login page
├── profile.php             # User profile page
├── register.php            # Registration page
├── tasks.php               # Tasks management page
│
├── database.sql            # Initial database schema
├── update_database.sql     # Database updates/migrations
│
└── README.md               # Project documentation
```

## Removed Files (Cleaned Up)

### Node.js Artifacts (Not needed for PHP project):
- ❌ package.json
- ❌ package-lock.json
- ❌ node_modules/

### Test Files:
- ❌ test-database.php
- ❌ test-dates.php
- ❌ api/php-test.php
- ❌ api/simple-test.txt
- ❌ api/test.php
- ❌ api/test-api.php

### Duplicate/Old Files:
- ❌ api/tasks-minimal.php

## Core Technologies

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Framework:** Bootstrap 5.3.7
- **Icons:** Bootstrap Icons 1.11.0

## Database Setup

1. Create database: `dailydo`
2. Run `database.sql` for initial setup
3. Run `update_database.sql` for updates

## Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server (or PHP built-in server)

## Running the Project

### Using XAMPP:
1. Start Apache & MySQL in XAMPP Control Panel
2. Access via: `http://localhost/To-Do-List/`

### Using PHP Built-in Server:
```bash
D:\xampp\php\php.exe -S localhost:8080 -t D:\To-Do-List
```
Access via: `http://localhost:8080/`

## API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/register.php` | POST | User registration |
| `/api/login.php` | POST | User login |
| `/api/logout.php` | POST | User logout |
| `/api/user.php` | GET | Get user data |
| `/api/profile-clean.php` | GET/PUT | Get/Update profile |
| `/api/profile.php` | POST | Change password |
| `/api/tasks.php` | GET/POST/PUT/DELETE | Tasks CRUD |
| `/api/admin.php` | GET/PUT/DELETE | Admin operations |

## Project Status

✅ **Clean and Production Ready**
- All test files removed
- No unnecessary dependencies
- Pure PHP/MySQL implementation
- Optimized file structure
