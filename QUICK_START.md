# Quick Start - Subdirectory Deployment

This guide will help you quickly configure the application for subdirectory hosting like `http://160.250.204.218/saas-platform`.

## Step 1: Configure Environment

Copy the production environment template:

```bash
cp .env.production.example .env
```

Edit `.env` and update these key settings:

```env
APP_URL=http://160.250.204.218
APP_BASE_PATH=/saas-platform
APP_DOMAIN=160.250.204.218
```

## Step 2: Configure Database

Update database credentials in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Step 3: Deploy

### Automatic Deployment (Recommended)

**Windows:**
```cmd
deploy.bat
```

**Linux/Mac:**
```bash
chmod +x deploy.sh
./deploy.sh
```

### Manual Deployment

```bash
# 1. Generate key
php artisan key:generate

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install

# 3. Build assets with subdirectory path
set ASSET_URL=/saas-platform  # Windows
export ASSET_URL=/saas-platform  # Linux/Mac
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set permissions
chmod -R 755 storage bootstrap/cache  # Linux/Mac
```

## Step 4: Web Server Configuration

### Apache (with symbolic link)

```bash
# Create symlink
ln -s /path/to/saas-platform/public /var/www/html/saas-platform
```

### Apache (with Alias)

Add to your Apache config or VirtualHost:

```apache
Alias /saas-platform "/path/to/saas-platform/public"

<Directory "/path/to/saas-platform/public">
    AllowOverride All
    Require all granted
</Directory>
```

## Step 5: Verify

Visit: `http://160.250.204.218/saas-platform`

## Troubleshooting

### Assets not loading?
```bash
# Rebuild with ASSET_URL set
set ASSET_URL=/saas-platform && npm run build  # Windows
export ASSET_URL=/saas-platform && npm run build  # Linux
```

### Routes not working?
```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 500 Error?
```bash
# Check permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Environment Variables Summary

| Variable | Example | Description |
|----------|---------|-------------|
| `APP_URL` | `http://160.250.204.218` | Base server URL (no subdirectory) |
| `APP_BASE_PATH` | `/saas-platform` | Subdirectory path (empty for root) |
| `APP_DOMAIN` | `160.250.204.218` | Domain for routing |
| `ASSET_URL` | `/saas-platform` | Build-time variable for assets |

## Need More Help?

See detailed documentation: [SUBDIRECTORY_DEPLOYMENT.md](SUBDIRECTORY_DEPLOYMENT.md)
