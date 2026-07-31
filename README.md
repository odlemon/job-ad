# Job Advertisement Platform

A modern Laravel-based job advertisement platform with Filament PHP admin panel.

## 🚀 Features

- **Company Management**: Manage companies posting jobs
- **Job Categories**: Organize jobs by categories
- **Job Advertisements**: Create and manage job postings with rich details
- **Job Applications**: Track and manage job applications
- **Admin Panel**: Beautiful Filament PHP admin interface

## 📋 Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

1. **Clone the repository** (if applicable) or navigate to the project directory

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**:
   ```bash
   php artisan migrate
   ```

6. **Create admin user**:
   ```bash
   php artisan make:filament-user
   ```

7. **Start the development server**:
   ```bash
   php artisan serve
   ```

   Or double-click `serve.bat` in the project root.

   Open: **http://127.0.0.1:8000**

8. **Access the admin panel**:
   - Navigate to: `http://localhost:8000/admin`
   - Login with your admin credentials

## 📁 Project Structure

```
app/
├── Filament/
│   └── Resources/          # Filament admin panel resources
│       ├── CompanyResource.php
│       ├── JobCategoryResource.php
│       ├── JobAdvertisementResource.php
│       └── JobApplicationResource.php
├── Http/
│   └── Controllers/        # API/Web controllers
├── Models/                 # Eloquent models
│   ├── Company.php
│   ├── JobCategory.php
│   ├── JobAdvertisement.php
│   └── JobApplication.php
├── Repositories/           # Data access layer (Repository pattern)
├── Services/              # Business logic layer
└── Resources/             # API resources (if needed)

database/
└── migrations/             # Database migrations
    ├── create_companies_table.php
    ├── create_job_categories_table.php
    ├── create_job_advertisements_table.php
    └── create_job_applications_table.php
```

## 🗄️ Database Schema

### Companies
- Basic company information (name, description, website, etc.)
- Logo upload support
- Industry and company size
- Active/inactive status

### Job Categories
- Category name and description
- Icon support
- Sort order for display
- Active/inactive status

### Job Advertisements
- Full job details (title, description, requirements, benefits)
- Employment type and experience level
- Salary range and currency
- Location and remote work options
- Application deadline
- Status management (draft, published, closed, archived)
- View and application counters

### Job Applications
- Applicant information
- Cover letter and resume upload
- Application status tracking
- Internal notes for recruiters

## 🎨 Admin Panel Features

The Filament admin panel provides:

- **CRUD Operations**: Full create, read, update, delete for all resources
- **Advanced Filtering**: Filter by status, company, category, etc.
- **Search Functionality**: Search across all relevant fields
- **Rich Forms**: Organized sections with validation
- **File Uploads**: Support for company logos and resumes
- **Status Badges**: Visual status indicators
- **Relationship Management**: Easy selection of related records

## 🖥️ Development Server (`php artisan serve`)

### Quick start

From the project root:

```bash
php artisan serve
```

Alternative: double-click **`serve.bat`**.

The app should be available at **http://127.0.0.1:8000**.

You may see a harmless PHP warning about the `imagick` extension at startup. The server still runs normally without it.

### Problem that was fixed (Windows)

On this machine, `php artisan serve` was failing with errors such as:

- `Could not write the development server router. Check storage/ permissions.`
- `Failed opening required '...\public\index.php'`
- `Failed opening required '...\vendor\...\server.php'`

The server would not start, or would stop immediately.

### Root causes

1. **`public/index.php` was missing** — Laravel’s normal web entry point was not present.
2. **The default serve flow needed a router script** — when `index.php` is missing, PHP’s built-in server needs a separate router file.
3. **Router file creation failed** — writes to `app/Console/Commands/`, `storage/`, and sometimes `%TEMP%` were blocked by file permissions on Windows.
4. **Vendor router missing** — `vendor/laravel/framework/.../server.php` was also absent, so Laravel’s stock serve command could not fall back cleanly.

### Fix applied

These steps were taken to make local development reliable:

#### 1. Restore `public/index.php`

The standard Laravel front controller was recreated at `public/index.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
```

If this file is ever deleted again, you can also run:

```bash
php artisan app:restore-public-index
```

#### 2. Add a custom `serve` command (`DevServe`)

File: `app/Console/Commands/DevServe.php`

Registered in `bootstrap/app.php`:

```php
->withCommands([
    \App\Console\Commands\DevServe::class,
])
```

Behavior:

- **If `public/index.php` exists** (normal case): run  
  `php -S 127.0.0.1:8000 -t public`  
  No router file is written. No extra permissions are needed.
- **If `public/index.php` is missing** (fallback): try to use or create a router script in `app/Console/Commands/serve-router.php` or `%TEMP%\job-ad-serve-router.php`.

This avoids the storage-permissions error in the common case.

#### 3. Add `serve.bat`

File: `serve.bat` in the project root — a one-click launcher that runs:

```bat
php -d display_startup_errors=0 artisan serve
```

### If the server breaks again

1. **Confirm `public/index.php` exists**:
   ```powershell
   Test-Path public\index.php
   ```
   If `False`, restore it (see step 1 above) or run `php artisan app:restore-public-index`.

2. **Confirm the custom serve command is registered** — check that `bootstrap/app.php` includes `DevServe::class` in `withCommands([...])`.

3. **Start the server**:
   ```bash
   php artisan serve
   ```

4. **Direct fallback** (bypasses Artisan, useful for debugging):
   ```bash
   php -d display_startup_errors=0 -S 127.0.0.1:8000 -t public
   ```

5. **Verify it is working** — open http://127.0.0.1:8000 in a browser. You should get HTTP 200, not a connection error.

### Files involved in this fix

| File | Purpose |
|------|---------|
| `public/index.php` | Laravel web entry point |
| `app/Console/Commands/DevServe.php` | Custom `php artisan serve` command |
| `app/Console/Commands/RestorePublicIndex.php` | Artisan helper to restore `public/index.php` |
| `bootstrap/app.php` | Registers `DevServe` |
| `serve.bat` | Windows launcher for the dev server |

## 🔧 Development

### Creating a New Model

1. Create migration:
   ```bash
   php artisan make:migration create_[table_name]_table
   ```

2. Create model:
   ```bash
   php artisan make:model ModelName
   ```

3. Create Filament resource:
   ```bash
   php artisan make:filament-resource ModelName
   ```

### Running Tests

```bash
php artisan test
```

### Code Style

The project follows PSR-12 coding standards. Use Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

## 📝 Notes

- All models use soft deletes for data preservation
- Foreign key constraints ensure data integrity
- Indexes are added for performance optimization
- The project uses industry-standard patterns (Repository, Service layers)

## 🤝 Contributing

When contributing to this project:

1. Follow the coding standards outlined in `.cursorrules`
2. Write tests for new features
3. Update documentation as needed
4. Use meaningful commit messages

## 📄 License

[Your License Here]

## 🙏 Acknowledgments

- Laravel Framework
- Filament PHP
- All contributors
