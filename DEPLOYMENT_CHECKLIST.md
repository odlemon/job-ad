# Quick Deployment Checklist

Use this checklist to track your deployment progress.

## Pre-Deployment

- [ ] Server: Ubuntu 22.04/24.04 LTS
- [ ] Root/sudo access confirmed
- [ ] Domain name configured (DNS pointing to server IP)
- [ ] Git repository access ready

## Installation Steps

### System Setup
- [ ] System updated (`sudo apt update && sudo apt upgrade`)
- [ ] PHP 8.2+ installed with all extensions
- [ ] MySQL installed and secured
- [ ] Nginx installed
- [ ] Composer installed
- [ ] Node.js 20+ and npm installed

### Database Setup
- [ ] Database created (`job_ad_db`)
- [ ] Database user created (`job_ad_user`)
- [ ] User permissions granted
- [ ] Password saved securely

### Application Setup
- [ ] Repository cloned to `/var/www/job-ad`
- [ ] Composer dependencies installed (`composer install --no-dev`)
- [ ] Node dependencies installed (`npm install`)
- [ ] Assets built (`npm run build`)
- [ ] `.env` file created from `.env.example`
- [ ] Application key generated (`php artisan key:generate`)
- [ ] `.env` configured with:
  - [ ] Database credentials
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL` set correctly

### Database & Storage
- [ ] Migrations run (`php artisan migrate --force`)
- [ ] Storage link created (`php artisan storage:link`)
- [ ] Permissions set correctly:
  - [ ] `storage/` directory: 775
  - [ ] `bootstrap/cache/` directory: 775
  - [ ] Ownership: `www-data:www-data`

### Web Server
- [ ] Nginx configuration created
- [ ] Site enabled (`ln -s`)
- [ ] Nginx config tested (`nginx -t`)
- [ ] Nginx restarted
- [ ] PHP-FPM configured
- [ ] PHP-FPM restarted

### Security
- [ ] Firewall configured (UFW)
- [ ] SSH access allowed
- [ ] HTTP/HTTPS ports open
- [ ] SSL certificate installed (Let's Encrypt)
- [ ] File permissions secured

### Optimization
- [ ] Config cached (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Views cached (`php artisan view:cache`)
- [ ] Autoloader optimized (`composer dump-autoload --optimize`)

### Optional Services
- [ ] Queue worker configured (Supervisor)
- [ ] Scheduled tasks configured (Cron)
- [ ] Monitoring set up

## Testing

- [ ] Application loads in browser
- [ ] Homepage displays correctly
- [ ] Navigation works (test page transitions)
- [ ] Database queries work
- [ ] File uploads work (if applicable)
- [ ] No errors in browser console
- [ ] No errors in server logs

## Post-Deployment

- [ ] Backups configured
- [ ] Log rotation set up
- [ ] Monitoring active
- [ ] Documentation updated
- [ ] Team notified

## Quick Commands Reference

```bash
# Check services
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql

# View logs
sudo tail -f /var/log/nginx/error.log
tail -f /var/www/job-ad/storage/logs/laravel.log

# Clear caches
cd /var/www/job-ad
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```
