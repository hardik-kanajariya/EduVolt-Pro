# Installation Requirements

## System Requirements

### Server Requirements
- **Operating System**: Linux (Ubuntu 20.04+ recommended), Windows Server 2019+, or macOS 10.15+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 8.2 or higher
- **Database**: MySQL 8.0+ or PostgreSQL 13+
- **Memory**: Minimum 2GB RAM (4GB+ recommended)
- **Storage**: Minimum 10GB free space (20GB+ recommended)

### PHP Extensions Required
```bash
# Essential PHP Extensions
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD PHP Extension
- ZIP PHP Extension
- Intl PHP Extension
```

### Database Requirements
- **MySQL**: 8.0+ with InnoDB storage engine
- **PostgreSQL**: 13+ (alternative to MySQL)
- **Character Set**: UTF8MB4 (for MySQL)
- **Collation**: utf8mb4_unicode_ci (for MySQL)

### Composer Requirements
- **Composer**: 2.5+ (PHP dependency manager)

### Optional But Recommended
- **Redis**: 6.0+ (for caching and sessions)
- **Memcached**: 1.6+ (alternative caching solution)
- **Elasticsearch**: 8.0+ (for advanced search features)
- **Node.js**: 18+ (for asset compilation)
- **NPM/Yarn**: Latest version

## Production Environment Specific

### Web Server Configuration
```apache
# Apache .htaccess requirements
- mod_rewrite enabled
- AllowOverride All
- Document root pointed to /public directory
```

```nginx
# Nginx configuration requirements
- PHP-FPM configured
- Proper location blocks for Laravel
- Static file serving optimized
```

### SSL/TLS Requirements
- **SSL Certificate**: Valid SSL certificate required for production
- **HTTPS**: Force HTTPS redirects recommended
- **TLS Version**: TLS 1.2+ required

### Email Service
- **SMTP Server**: Configured SMTP server
- **Alternative**: Mailgun, SendGrid, or AWS SES integration

### File Storage
- **Local Storage**: Adequate disk space for file uploads
- **Cloud Storage**: AWS S3, DigitalOcean Spaces (optional)

## Development Environment

### Additional Development Tools
- **Git**: 2.30+ for version control
- **IDE**: PhpStorm, VS Code, or similar
- **Xdebug**: 3.0+ for debugging (optional)
- **Laravel Debugbar**: Development debugging tool

### Browser Requirements
- **Modern Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **JavaScript**: ES6+ support required
- **Local Storage**: Browser local storage support

## Security Requirements

### File Permissions
```bash
# Recommended file permissions
Storage directories: 755
Cache directories: 755
Bootstrap/cache: 755
Web accessible files: 644
Configuration files: 600
```

### Environment Security
- **Environment Variables**: Secure .env file configuration
- **APP_KEY**: Properly generated application key
- **Database Credentials**: Secure database user with limited privileges
- **CORS**: Proper CORS configuration if needed

## Performance Considerations

### Minimum Performance Specs
- **CPU**: 2 cores minimum (4+ cores recommended)
- **RAM**: 2GB minimum (4GB+ recommended for production)
- **Storage**: SSD recommended for database and cache
- **Network**: Stable internet connection for external services

### Optimization Requirements
- **OPcache**: PHP OPcache enabled
- **Caching**: Redis or Memcached for session/cache storage
- **Queue Workers**: For background job processing
- **CDN**: CloudFlare or similar for static assets (recommended)

## Verification Checklist

Before installation, verify:
- [ ] PHP version meets requirements
- [ ] All required PHP extensions installed
- [ ] Database server accessible and configured
- [ ] Composer installed and accessible
- [ ] Web server properly configured
- [ ] SSL certificate ready (for production)
- [ ] Email service configured
- [ ] File permissions set correctly
- [ ] Adequate storage space available
- [ ] Network access to external services
