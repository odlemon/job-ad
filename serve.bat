@echo off
cd /d "%~dp0"
echo.
echo  Job Ad server: http://127.0.0.1:8000
echo  Press Ctrl+C to stop.
echo.
php -d display_startup_errors=0 artisan serve
pause
