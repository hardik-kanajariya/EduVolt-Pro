# EduVolt Pro Deployment Guide

## Overview
This guide explains how to deploy EduVolt Pro Laravel application to a VPS using Docker and GitHub Actions.

## Prerequisites

### VPS Requirements
- Ubuntu/Debian VPS with Docker and Docker Compose installed
- Nginx and MySQL already installed and running
- Git installed
- SSH access (username/password or SSH key)

### GitHub Secrets Required
You need to set the following secrets in your GitHub repository (Settings → Secrets and variables → Actions):

#### SSH Connection
- `VPS_HOST` - Your VPS IP address or domain
- `VPS_USER` - SSH username
- `VPS_PASSWORD` - SSH password (for password authentication)
- `SSH_PRIVATE_KEY` - SSH private key (for key-based authentication)
- `DEPLOY_PATH` - Path where your application will be deployed (e.g., `/var/www/eduvolt-pro`)

#### Application Configuration
- `APP_KEY` - Laravel application key (generate with `php artisan key:generate --show`)
- `APP_URL` - Your application URL (e.g., `https://yourdomain.com`)

#### Database Configuration
- `DB_HOST` - Database host (use `127.0.0.1` for local MySQL)
- `DB_PORT` - Database port (default: `3306`)
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

#### Mail Configuration
- `MAIL_MAILER` - Mail driver (smtp, sendmail, etc.)
- `MAIL_HOST` - SMTP host
- `MAIL_PORT` - SMTP port
- `MAIL_USERNAME` - SMTP username
- `MAIL_PASSWORD` - SMTP password
- `MAIL_ENCRYPTION` - Encryption type (tls, ssl, null)
- `MAIL_FROM_ADDRESS` - From email address

#### AWS Configuration (Optional)
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_DEFAULT_REGION`
- `AWS_BUCKET`

## Deployment Methods

### Method 1: SSH Key Authentication (Recommended)
Use the `deploy.yml` workflow file. This method is more secure.

### Method 2: Username/Password Authentication
Use the `deploy-password.yml` workflow file if you prefer password authentication.

## VPS Setup

### 1. Install Docker and Docker Compose
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Add user to docker group
sudo usermod -aG docker $USER
```

### 2. Clone Repository
```bash
# Create deployment directory
sudo mkdir -p /var/www/eduvolt-pro
sudo chown $USER:$USER /var/www/eduvolt-pro

# Clone repository
cd /var/www/eduvolt-pro
git clone https://github.com/your-username/EduVolt-Pro.git .
```

### 3. Configure Nginx (Reverse Proxy)
Create nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/eduvolt-pro
```

Add the following configuration:
```nginx
server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/eduvolt-pro /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Configure MySQL Database
```bash
# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE eduvault_pro;
CREATE USER 'eduvolt_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON eduvault_pro.* TO 'eduvolt_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Deployment Process

### Automatic Deployment
1. Push your code to the `master` or `main` branch
2. GitHub Actions will automatically deploy to your VPS
3. The deployment includes:
   - Pulling latest code
   - Creating `.env` file line by line
   - Building Docker containers
   - Running migrations and seeders
   - Caching configurations
   - Setting proper permissions

### Manual Deployment
You can also trigger deployment manually from GitHub Actions:
1. Go to your repository on GitHub
2. Click on "Actions" tab
3. Select the deployment workflow
4. Click "Run workflow"

## Monitoring

### Check Application Status
```bash
# Check container status
docker-compose ps

# View logs
docker-compose logs -f app

# Check nginx status
sudo systemctl status nginx

# Check MySQL status
sudo systemctl status mysql
```

### Application URLs
- Application: `http://yourdomain.com`
- Admin Panel: `http://yourdomain.com/admin`
- School Panel: `http://yourdomain.com/school`
- Faculty Panel: `http://yourdomain.com/faculty`
- Student Panel: `http://yourdomain.com/student`
- Parent Panel: `http://yourdomain.com/parent`

## Troubleshooting

### Common Issues

#### 1. Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

#### 2. Database Connection Issues
- Check MySQL service: `sudo systemctl status mysql`
- Verify database credentials in GitHub secrets
- Check if database exists

#### 3. Container Build Issues
```bash
# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

#### 4. SSL Certificate (Optional)
To add SSL certificate using Let's Encrypt:
```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## Security Considerations

1. **Firewall**: Configure UFW to allow only necessary ports
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

2. **SSH**: Disable password authentication if using SSH keys
3. **Database**: Use strong passwords and restrict access
4. **Updates**: Keep system and Docker images updated regularly

## Backup Strategy

1. **Database Backup**:
```bash
# Create backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
docker-compose exec mysql mysqldump -u root -p eduvault_pro > backup_$DATE.sql
```

2. **File Backup**: Backup storage directory and .env file regularly

## Support

For issues and support:
1. Check application logs: `docker-compose logs`
2. Review GitHub Actions workflow logs
3. Check system logs: `sudo journalctl -u nginx` or `sudo journalctl -u mysql`
