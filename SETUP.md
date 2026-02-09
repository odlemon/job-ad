# Setup Guide - Job Advertisement Platform

## Prerequisites

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer (installed globally)
- Node.js & NPM (for frontend assets)

## Step-by-Step Setup

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (if you have frontend assets)
npm install
```

### 2. Environment Configuration

Create a `.env` file from the example (if it doesn't exist):

```bash
# On Windows PowerShell
Copy-Item .env.example .env

# Or manually create .env file
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Configure Database

**Option 1: Using Database URL (Recommended - Simple!)**

Edit your `.env` file and add your database URL:

```env
DB_CONNECTION=mysql
DB_URL=mysql://username:password@host:port/database_name
```

**Example:**
```env
DB_CONNECTION=mysql
DB_URL=mysql://root:yourpassword@127.0.0.1:3306/job_platform
```

**Option 2: Using Individual Fields**

If you prefer separate fields:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Important:** Make sure your MySQL database exists. Create it if needed:

```sql
CREATE DATABASE job_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Note:** See `DATABASE_URL_FORMAT.md` for more details on database URL format.

### 5. Run Migrations

```bash
php artisan migrate
```

This will create all the necessary tables:
- `companies`
- `job_categories`
- `job_advertisements`
- `job_applications`
- `users` (Laravel default)
- `migrations` (Laravel default)

### 6. Create Admin User

Create your first admin user for the Filament admin panel:

```bash
php artisan make:filament-user
```

You'll be prompted to enter:
- Name
- Email
- Password

### 7. Start Development Server

```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

### 8. Access the Admin Panel

Navigate to: **http://localhost:8000/admin**

Login with the admin credentials you created in step 6.

## Quick Start (All Commands)

```bash
# 1. Install dependencies
composer install

# 2. Create .env file (if needed)
# Copy .env.example to .env and edit it

# 3. Generate key
php artisan key:generate

# 4. Configure database in .env file
# Add: DB_URL=mysql://username:password@host:port/database_name
# Or use individual DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD fields

# 5. Run migrations
php artisan migrate

# 6. Create admin user
php artisan make:filament-user

# 7. Start server
php artisan serve
```

## Troubleshooting

### Database Connection Error

If you get a database connection error:

1. **Check MySQL is running:**
   ```bash
   # Windows - Check MySQL service
   Get-Service MySQL*
   ```

2. **Verify credentials in `.env`:**
   - Make sure `DB_DATABASE` exists
   - Check `DB_USERNAME` and `DB_PASSWORD` are correct
   - Verify `DB_HOST` and `DB_PORT`

3. **Test connection:**
   ```bash
   php artisan tinker
   # Then in tinker:
   DB::connection()->getPdo();
   ```

### Permission Errors

If you get permission errors on Windows:

```bash
# Make sure storage and bootstrap/cache are writable
# Laravel should handle this automatically, but if not:
icacls storage /grant Users:F /T
icacls bootstrap\cache /grant Users:F /T
```

### Port Already in Use

If port 8000 is already in use:

```bash
# Use a different port
php artisan serve --port=8001
```

## Development Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations (fresh - drops all tables)
php artisan migrate:fresh

# Run migrations with seeders (if you create any)
php artisan migrate:fresh --seed

# Generate Filament resource
php artisan make:filament-resource ModelName

# Run tests
php artisan test

# Code formatting (Laravel Pint)
./vendor/bin/pint
```

## Next Steps

1. **Access Admin Panel**: http://localhost:8000/admin
2. **Create Companies**: Add some companies through the admin panel
3. **Create Categories**: Add job categories
4. **Create Job Advertisements**: Post some jobs
5. **Test Applications**: Create job applications

## API Endpoints (if you add routes)

If you want to use the API controllers, add routes to `routes/api.php`:

```php
Route::apiResource('companies', \App\Http\Controllers\Api\CompanyController::class);
Route::apiResource('job-advertisements', \App\Http\Controllers\Api\JobAdvertisementController::class);
Route::apiResource('job-applications', \App\Http\Controllers\Api\JobApplicationController::class);
```

Then access via: `http://localhost:8000/api/companies`
