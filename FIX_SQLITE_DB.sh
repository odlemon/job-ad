#!/bin/bash
# Fix SQLite database file location

echo "Checking database file location..."

# Check if database directory exists
if [ ! -d "/var/www/projects/job-ad/database" ]; then
    echo "Creating database directory..."
    mkdir -p /var/www/projects/job-ad/database
fi

# Check if database file exists
if [ ! -f "/var/www/projects/job-ad/database/database.sqlite" ]; then
    echo "Database file not found. Checking git status..."
    
    # Check if it's in git
    cd /var/www/projects/job-ad
    git ls-files database/database.sqlite
    
    if [ $? -eq 0 ]; then
        echo "File is in git. Pulling latest changes..."
        git pull
    else
        echo "File not in git. Creating empty database..."
        touch database/database.sqlite
    fi
fi

# Set permissions
if [ -f "/var/www/projects/job-ad/database/database.sqlite" ]; then
    echo "Setting permissions..."
    sudo chmod 664 /var/www/projects/job-ad/database/database.sqlite
    sudo chown www-data:www-data /var/www/projects/job-ad/database/database.sqlite
    echo "✓ Database file ready!"
    ls -la /var/www/projects/job-ad/database/database.sqlite
else
    echo "✗ Database file still not found. Creating empty one..."
    touch /var/www/projects/job-ad/database/database.sqlite
    sudo chmod 664 /var/www/projects/job-ad/database/database.sqlite
    sudo chown www-data:www-data /var/www/projects/job-ad/database/database.sqlite
fi
