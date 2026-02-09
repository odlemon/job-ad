#!/bin/bash
# Fix PHP-FPM installation when systemd is not available

echo "Fixing PHP-FPM installation..."

# 1. Create required directories
mkdir -p /run/php
chown www-data:www-data /run/php 2>/dev/null || chown root:root /run/php

# 2. Create a dummy systemctl script to satisfy the package
cat > /usr/local/bin/systemctl << 'EOF'
#!/bin/bash
# Dummy systemctl for systems without systemd
case "$1" in
    start|stop|restart|reload|status|enable|disable)
        # Do nothing - we'll manage services manually
        exit 0
        ;;
    *)
        echo "Dummy systemctl - service management disabled"
        exit 0
        ;;
esac
EOF

chmod +x /usr/local/bin/systemctl

# 3. Add to PATH if not already there
export PATH=$PATH:/usr/local/bin:/usr/bin:/bin

# 4. Try to configure again
dpkg --configure -a

# 5. If still failing, force configure
if [ $? -ne 0 ]; then
    echo "Forcing configuration..."
    DEBIAN_FRONTEND=noninteractive dpkg --configure --force-all php8.2-fpm php8.2
fi

# 6. Verify PHP-FPM files are installed
if [ -f /usr/sbin/php-fpm8.2 ]; then
    echo "PHP-FPM binary found at /usr/sbin/php-fpm8.2"
    
    # 7. Test PHP-FPM configuration
    /usr/sbin/php-fpm8.2 -t
    
    # 8. Start PHP-FPM manually
    echo "Starting PHP-FPM manually..."
    /usr/sbin/php-fpm8.2 --daemonize --fpm-config /etc/php/8.2/fpm/php-fpm.conf
    
    # 9. Check if it's running
    if ps aux | grep -q "[p]hp-fpm8.2"; then
        echo "✓ PHP-FPM is running!"
    else
        echo "⚠ PHP-FPM may not have started. Check logs:"
        echo "  tail -f /var/log/php8.2-fpm.log"
    fi
else
    echo "✗ PHP-FPM binary not found. Installation may have failed."
fi

echo ""
echo "Next steps:"
echo "1. Verify PHP: php -v"
echo "2. Check PHP-FPM: ps aux | grep php-fpm"
echo "3. Check socket: ls -la /run/php/php8.2-fpm.sock"
