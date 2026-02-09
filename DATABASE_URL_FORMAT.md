# Database URL Configuration

## Format

Laravel supports using a single `DB_URL` environment variable instead of separate database configuration fields.

### MySQL/MariaDB URL Format

```
mysql://[username]:[password]@[host]:[port]/[database]?[options]
```

### Examples

**Local MySQL:**
```
DB_URL=mysql://root:password@127.0.0.1:3306/job_platform
```

**Local MySQL (no password):**
```
DB_URL=mysql://root@127.0.0.1:3306/job_platform
```

**Remote MySQL:**
```
DB_URL=mysql://myuser:mypassword@db.example.com:3306/job_platform
```

**With SSL:**
```
DB_URL=mysql://user:password@host:3306/database?sslmode=require
```

### In Your `.env` File

Simply add this line (replace with your actual credentials):

```env
DB_CONNECTION=mysql
DB_URL=mysql://username:password@host:port/database_name
```

**Example:**
```env
DB_CONNECTION=mysql
DB_URL=mysql://root:yourpassword@127.0.0.1:3306/job_platform
```

### Notes

- When `DB_URL` is set, Laravel will automatically parse it
- Individual `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` become optional
- You can still use individual fields if you prefer
- The `DB_CONNECTION` should still be set to `mysql` (or `mariadb`, `pgsql`, etc.)

### URL Encoding

If your password contains special characters, URL encode them:
- `@` becomes `%40`
- `#` becomes `%23`
- `%` becomes `%25`
- etc.

**Example with special characters:**
```
DB_URL=mysql://user:p%40ssw%23rd@host:3306/database
```
