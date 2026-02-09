# Architecture Documentation

## Overview

This project follows a **Repository + Service Layer** architecture pattern to ensure clean separation of concerns and maintainability.

## Architecture Layers

```
┌─────────────────────────────────────────┐
│         HTTP Layer (Controllers)        │
│  - Handle requests/responses            │
│  - Input validation                     │
│  - Call Services only                  │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│         Business Logic (Services)       │
│  - All business rules                   │
│  - Data transformations                 │
│  - Use Repositories for data access     │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│      Data Access (Repositories)         │
│  - All database queries                 │
│  - Eager loading                        │
│  - Return models/collections            │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│         Data Models (Eloquent)          │
│  - Relationships                        │
│  - Attributes & casts                   │
│  - No business logic                    │
└─────────────────────────────────────────┘
```

## Directory Structure

```
app/
├── Http/
│   └── Controllers/
│       └── Api/                    # API Controllers
│           ├── CompanyController.php
│           ├── JobAdvertisementController.php
│           └── JobApplicationController.php
│
├── Services/                        # Business Logic Layer
│   ├── CompanyService.php
│   ├── JobCategoryService.php
│   ├── JobAdvertisementService.php
│   └── JobApplicationService.php
│
├── Repositories/                    # Data Access Layer
│   ├── Contracts/                   # Repository Interfaces
│   │   ├── CompanyRepositoryInterface.php
│   │   ├── JobCategoryRepositoryInterface.php
│   │   ├── JobAdvertisementRepositoryInterface.php
│   │   └── JobApplicationRepositoryInterface.php
│   │
│   └──                             # Repository Implementations
│       ├── CompanyRepository.php
│       ├── JobCategoryRepository.php
│       ├── JobAdvertisementRepository.php
│       └── JobApplicationRepository.php
│
└── Models/                          # Eloquent Models
    ├── Company.php
    ├── JobCategory.php
    ├── JobAdvertisement.php
    └── JobApplication.php
```

## Layer Responsibilities

### 1. Controllers (`app/Http/Controllers/`)

**Responsibilities:**
- Handle HTTP requests and responses
- Validate input data
- Call Services (never Models or Repositories directly)
- Return JSON responses or views

**Example:**
```php
class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $service
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([...]);
        $company = $this->service->create($validated);
        return response()->json($company, 201);
    }
}
```

**❌ DON'T:**
- Put business logic here
- Query database directly
- Call Repositories directly
- Call Models directly

### 2. Services (`app/Services/`)

**Responsibilities:**
- Contain ALL business logic
- Handle data transformations
- Implement business rules
- Use Repositories for data access

**Example:**
```php
class CompanyService
{
    public function __construct(
        private CompanyRepositoryInterface $repository
    ) {}

    public function create(array $data): Company
    {
        // Business logic: Auto-generate slug
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $this->repository->create($data);
    }
}
```

**✅ DO:**
- Put all business logic here
- Transform data before saving
- Validate business rules
- Use Repositories for data access

**❌ DON'T:**
- Query database directly (use Repositories)
- Handle HTTP concerns
- Return HTTP responses

### 3. Repositories (`app/Repositories/`)

**Responsibilities:**
- Handle ALL database queries
- Use eager loading to prevent N+1 queries
- Return models or collections
- Implement Repository Interfaces

**Example:**
```php
class CompanyRepository implements CompanyRepositoryInterface
{
    public function find(int $id): ?Company
    {
        return Company::with(['jobAdvertisements'])->find($id);
    }
    
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Company::paginate($perPage);
    }
}
```

**✅ DO:**
- Write all database queries here
- Use eager loading
- Return Eloquent models/collections
- Keep queries simple and focused

**❌ DON'T:**
- Include business logic
- Transform data
- Handle HTTP concerns

### 4. Models (`app/Models/`)

**Responsibilities:**
- Define relationships
- Define fillable attributes
- Define casts
- No business logic

**Example:**
```php
class Company extends Model
{
    protected $fillable = ['name', 'slug', ...];
    
    public function jobAdvertisements(): HasMany
    {
        return $this->hasMany(JobAdvertisement::class);
    }
}
```

**✅ DO:**
- Define relationships
- Define attributes
- Use casts for data types

**❌ DON'T:**
- Include business logic
- Query database (use Repositories)
- Handle HTTP concerns

## Dependency Injection

All dependencies are injected via constructor and registered in `AppServiceProvider`:

```php
// AppServiceProvider.php
public function register(): void
{
    $this->app->bind(
        CompanyRepositoryInterface::class,
        CompanyRepository::class
    );
}
```

## Benefits of This Architecture

1. **Separation of Concerns**: Each layer has a single responsibility
2. **Testability**: Easy to mock repositories and test services
3. **Maintainability**: Changes in one layer don't affect others
4. **Reusability**: Services can be used by Controllers, Commands, Jobs, etc.
5. **Flexibility**: Easy to swap implementations (e.g., caching layer)

## Common Patterns

### Creating a New Entity

1. **Migration** → Define database structure
2. **Model** → Define relationships and attributes
3. **Repository Interface** → Define data access methods
4. **Repository Implementation** → Implement database queries
5. **Service** → Implement business logic
6. **Controller** → Handle HTTP requests
7. **Register** → Bind interface to implementation in AppServiceProvider

### Adding Business Logic

Always add business logic to Services, never to Controllers or Models:

```php
// ✅ CORRECT - In Service
public function publish(JobAdvertisement $job): JobAdvertisement
{
    $data = [
        'status' => 'published',
        'published_at' => now(),
    ];
    return $this->repository->update($job, $data);
}

// ❌ WRONG - In Controller
public function publish(int $id)
{
    $job = JobAdvertisement::find($id);
    $job->status = 'published';  // Business logic in controller!
    $job->save();
}
```

## Testing Strategy

- **Unit Tests**: Test Services with mocked Repositories
- **Integration Tests**: Test Repositories with database
- **Feature Tests**: Test Controllers with real Services

## Notes

- All Repositories implement Interfaces for easy mocking
- Services are injected into Controllers via constructor
- Repositories use eager loading to prevent N+1 queries
- Business logic is always in Services, never in Controllers or Models
