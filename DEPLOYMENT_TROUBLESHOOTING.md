# Deployment Troubleshooting Guide

## Issue: systemctl not found during PHP-FPM installation

This error occurs when systemd is not available or not in PATH. Here's how to fix it:

### Solution 1: Check if systemd is available

```bash
# Check if systemd exists
which systemctl
ls -la /usr/bin/systemctl

# Check if systemd is running
ps aux | grep systemd
```

### Solution 2: Fix PHP-FPM installation (if systemd is available but not in PATH)

```bash
# Find systemctl location
find /usr -name systemctl 2>/dev/null

# If found, create symlink or add to PATH
# Usually it's in /bin/systemctl or /usr/bin/systemctl
```

### Solution 3: Manual PHP-FPM configuration (if no systemd)

If you're in a Docker container or minimal system without systemd:

```bash
# Configure PHP-FPM manually
sudo dpkg --configure -a

# Try to reconfigure PHP packages
sudo dpkg-reconfigure php8.2-fpm

# If that fails, manually configure
sudo mkdir -p /run/php
sudo touch /run/php/php8.2-fpm.pid
sudo touch /run/php/php8.2-fpm.sock
```

### Solution 4: Complete fix for the current error

```bash
# First, try to fix broken packages
sudo dpkg --configure -a

# If that fails, force reinstall
sudo apt-get install --reinstall php8.2-fpm

# Or remove and reinstall
sudo apt-get remove --purge php8.2-fpm
sudo apt-get install php8.2-fpm

# After installation, manually start PHP-FPM
sudo /usr/sbin/php-fpm8.2 --daemonize --fpm-config /etc/php/8.2/fpm/php-fpm.conf
```

### Solution 5: Alternative - Use PHP-CGI or Apache module

If PHP-FPM continues to have issues:

**Option A: Install PHP-CGI instead**
```bash
sudo apt-get install php8.2-cgi
```

**Option B: Use Apache with mod_php**
```bash
sudo apt-get install apache2 libapache2-mod-php8.2
```

### Solution 6: Check your system type

```bash
# Check if you're in a container
cat /proc/1/cgroup

# Check OS info
cat /etc/os-release
uname -a
```

### For Docker/Container environments:

If you're in a Docker container, you may need to:

1. **Start PHP-FPM manually in foreground:**
```bash
/usr/sbin/php-fpm8.2 -F
```

2. **Or use a supervisor/init system:**
```bash
# Install supervisor
apt-get install supervisor

# Create PHP-FPM supervisor config
cat > /etc/supervisor/conf.d/php-fpm.conf << EOF
[program:php-fpm]
command=/usr/sbin/php-fpm8.2 -F
autostart=true
autorestart=true
EOF

supervisord
```

### Quick Fix Command Sequence

Try these commands in order:

```bash
# 1. Fix broken packages
sudo dpkg --configure -a

# 2. If systemctl exists but not found, check PATH
echo $PATH
export PATH=$PATH:/usr/bin:/bin:/usr/sbin:/sbin

# 3. Try installing again
sudo apt-get install -f
sudo apt-get install --reinstall php8.2-fpm

# 4. Manually start PHP-FPM
sudo /usr/sbin/php-fpm8.2 --daemonize

# 5. Verify it's running
ps aux | grep php-fpm
```

### Verify PHP-FPM is working

```bash
# Check if PHP-FPM process is running
ps aux | grep php-fpm

# Check if socket file exists
ls -la /run/php/php8.2-fpm.sock
# or
ls -la /var/run/php/php8.2-fpm.sock

# Test PHP
php -v

# Test PHP-FPM config
sudo /usr/sbin/php-fpm8.2 -t
```

### Continue with deployment

Once PHP-FPM is working, continue with the deployment steps. You can verify it's working by:

```bash
# Check PHP version
php -v

# Check PHP extensions
php -m

# Test PHP-FPM
sudo /usr/sbin/php-fpm8.2 -t
```

Then proceed with the rest of the deployment guide.
