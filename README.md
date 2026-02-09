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
