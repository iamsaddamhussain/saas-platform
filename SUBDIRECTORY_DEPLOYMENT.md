# Subdirectory Deployment Configuration

This guide explains how to configure the application for subdirectory deployment (e.g., `http://160.250.204.218/saas-platform`).

## Environment Configuration

Update your `.env` file with the following settings:

### For Subdirectory Hosting (e.g., /saas-platform)

```env
APP_NAME="Your SaaS Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://160.250.204.218
APP_BASE_PATH=/saas-platform
APP_DOMAIN=160.250.204.218

# For tenant subdomains to work with subdirectory
# You'll need DNS wildcard or individual subdomain configuration
# Example: tenant1.160.250.204.218/saas-platform
```

### For Root Domain Hosting

```env
APP_NAME="Your SaaS Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.com
APP_BASE_PATH=
APP_DOMAIN=yourdomain.com
```

## Configuration Variables Explained

- **APP_URL**: The base URL of your server (without subdirectory)
- **APP_BASE_PATH**: The subdirectory path where the app is hosted (leave empty for root)
- **APP_DOMAIN**: The domain used for routing (for multi-tenancy support)

## Build Assets for Production

After updating your `.env` file, rebuild the assets:

```bash
# Set the ASSET_URL for subdirectory
export ASSET_URL=/saas-platform
npm run build

# Or on Windows
set ASSET_URL=/saas-platform
npm run build
```

## Apache Configuration

The `.htaccess` file has been configured to work with subdirectories automatically.

### Web Server Document Root

Your web server's document root should point to the `public` directory:

```
Document Root: /path/to/saas-platform/public
```

### For Subdirectory Setup

If you're hosting in a subdirectory, create a symbolic link or configure Apache:

#### Option 1: Symbolic Link (Recommended)
```bash
# Create a symlink in your web root
ln -s /path/to/saas-platform/public /var/www/html/saas-platform
```

#### Option 2: Apache Alias
```apache
Alias /saas-platform /path/to/saas-platform/public

<Directory /path/to/saas-platform/public>
    AllowOverride All
    Require all granted
</Directory>
```

## Nginx Configuration

For Nginx users, use this configuration:

```nginx
location /saas-platform {
    alias /path/to/saas-platform/public;
    try_files $uri $uri/ @saas;
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}

location @saas {
    rewrite /saas-platform/(.*)$ /saas-platform/index.php?/$1 last;
}
```

## Multi-Tenancy with Subdirectory

**Important Note**: Subdomain-based multi-tenancy may have limitations when combined with subdirectory hosting. Consider these options:

### Option 1: Path-based Tenancy
Modify routes to use path-based tenancy instead:
```
http://160.250.204.218/saas-platform/tenant1
http://160.250.204.218/saas-platform/tenant2
```

### Option 2: Separate Domain
Use a dedicated domain for the application:
```
http://yoursaas.com (main)
http://tenant1.yoursaas.com
http://tenant2.yoursaas.com
```

### Option 3: Wildcard DNS + Subdirectory
Configure DNS wildcard records and proxy configuration:
```
*.160.250.204.218 -> Your server
http://tenant1.160.250.204.218/saas-platform
```

## Verification

After configuration, verify the setup:

1. Clear Laravel cache:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. Test the application:
   - Visit: `http://160.250.204.218/saas-platform`
   - Check that assets load correctly
   - Verify routes work as expected

## Troubleshooting

### Assets Not Loading
- Ensure `ASSET_URL=/saas-platform` is set during build
- Rebuild assets: `npm run build`
- Clear browser cache

### Routes Not Working
- Verify `.htaccess` is being read (check Apache `AllowOverride All`)
- Clear route cache: `php artisan route:clear`
- Check APP_URL and APP_BASE_PATH in `.env`

### 404 Errors
- Ensure symbolic link or alias is properly configured
- Check file permissions: `chmod -R 755 storage bootstrap/cache`
- Verify `public` directory is accessible

## Quick Setup Checklist

- [ ] Update `.env` with APP_URL, APP_BASE_PATH, APP_DOMAIN
- [ ] Set ASSET_URL environment variable
- [ ] Run `npm run build`
- [ ] Configure web server (symlink/alias)
- [ ] Clear Laravel caches
- [ ] Test the application

## Support

For issues, check:
- Laravel logs: `storage/logs/laravel.log`
- Web server error logs
- Browser console for asset loading errors
