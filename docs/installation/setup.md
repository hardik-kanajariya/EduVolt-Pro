# Setup Instructions

## Quick Installation Guide

### Step 1: Download and Extract
```bash
# Download the latest release
wget https://github.com/eduvaultpro/eduvaultpro/archive/main.zip

# Extract to your web directory
unzip main.zip
cd eduvaultpro-main
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies (if needed)
npm install

# Build assets
npm run build
```

### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Environment
Edit the `.env` file with your settings:

```env
# Application Configuration
APP_NAME="EduVault Pro"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduvault_pro
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="EduVault Pro"

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Step 5: Database Setup
```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE eduvault_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed
```

### Step 6: Storage Setup
```bash
# Create symbolic link for storage
php artisan storage:link

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 7: Install Filament
```bash
# Install Filament Admin Panel
php artisan filament:install --panels

# Create admin user
php artisan make:filament-user
```

## Detailed Installation Steps

### Prerequisites Verification
Before starting, ensure your system meets all requirements:

```bash
# Check PHP version
php --version

# Check required extensions
php -m | grep -E "(bcmath|ctype|curl|dom|fileinfo|json|mbstring|openssl|pcre|pdo|tokenizer|xml|gd|zip|intl)"

# Check Composer
composer --version

# Test database connection
mysql -u your_user -p -e "SELECT 1;"
```

### Web Server Configuration

#### Apache Configuration
Create or update your Apache virtual host:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot "/path/to/eduvaultpro/public"
    
    <Directory "/path/to/eduvaultpro/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/eduvaultpro_error.log
    CustomLog ${APACHE_LOG_DIR}/eduvaultpro_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot "/path/to/eduvaultpro/public"
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    
    <Directory "/path/to/eduvaultpro/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/eduvaultpro_ssl_error.log
    CustomLog ${APACHE_LOG_DIR}/eduvaultpro_ssl_access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    listen 443 ssl;
    server_name your-domain.com;
    root /path/to/eduvaultpro/public;
    
    # SSL Configuration
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Production Optimization

```bash
# Optimize for production
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache

# Optimize assets
npm run build
```

### Queue Workers Setup (Production)

Create a supervisor configuration file `/etc/supervisor/conf.d/eduvaultpro-worker.conf`:

```ini
[program:eduvaultpro-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/eduvaultpro/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/eduvaultpro-worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start eduvaultpro-worker:*
```

### Scheduler Setup

Add to crontab (`crontab -e`):

```bash
* * * * * cd /path/to/eduvaultpro && php artisan schedule:run >> /dev/null 2>&1
```

### Final Verification

```bash
# Test the application
php artisan test

# Check system health
php artisan health:check

# Verify database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Test email configuration
php artisan tinker --execute="Mail::raw('Test email', function(\$message) { \$message->to('test@example.com')->subject('Test'); });"
```

## Post-Installation Steps

1. **Access the Admin Panel**: Visit `https://your-domain.com/admin`
2. **Login with Admin Credentials**: Use the credentials created during setup
3. **Configure System Settings**: Navigate to Settings > System Configuration
4. **Set Up Academic Calendar**: Configure academic years, terms, and holidays
5. **Create User Roles**: Set up custom roles and permissions
6. **Import Initial Data**: Upload student lists, teacher data, etc.
7. **Test All Features**: Verify all modules are working correctly

## Troubleshooting Common Issues

### Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

### Database Connection Issues
```bash
# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Reset database (if needed)
php artisan migrate:reset
php artisan migrate
php artisan db:seed
```

### Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

For more troubleshooting help, see [Troubleshooting Guide](troubleshooting.md).
