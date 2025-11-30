# Deployment Guide: Fixing Image Paths on Live Server

## Overview
This guide explains how to deploy the image path fixes to your live server. The changes enable images to display correctly across different server environments (localhost vs production).

## What Was Changed

### New Files
- **`config/config.js.php`** - Dynamic configuration file that outputs JavaScript variables based on environment settings

### Modified Files
- **`js/product.js`** - Now uses `window.APP_CONFIG.BASE_PATH`
- **`js/cart.js`** - Now uses `window.APP_CONFIG.BASE_PATH`
- **`js/checkout.js`** - Now uses `window.APP_CONFIG.BASE_PATH`
- **`login/seller_dashboard.php`** - Includes `config.js.php` script
- **`login/buyer_dashboard.php`** - Includes `config.js.php` script

## Deployment Steps

### Step 1: Update Your Local `.env` File
Ensure your local `.env` file has the correct settings:

```env
APP_BASE_URL=http://localhost:8888/register_sample
APP_ENVIRONMENT=development
```

### Step 2: Commit and Push Changes
```bash
cd /Applications/MAMP/htdocs/register_sample
git add .
git commit -m "Fix: Implement dynamic base path configuration for images"
git push origin main
```

### Step 3: Deploy to Live Server
Pull the latest changes on your live server:

```bash
# SSH into your live server, then:
cd /path/to/your/project
git pull origin main
```

### Step 4: Create `.env` File on Live Server
**CRITICAL:** The `.env` file is gitignored, so you must create it manually on the live server.

```bash
# On your live server:
nano .env
```

Add the following content (adjust the URL to match your live server):

```env
# Database Configuration
DB_HOST=localhost
DB_NAME=your_live_database_name
DB_USER=your_live_database_user
DB_PASSWORD=your_live_database_password

# Paystack Configuration
PAYSTACK_SECRET_KEY=your_live_paystack_secret_key
PAYSTACK_PUBLIC_KEY=your_live_paystack_public_key

# Application Configuration
APP_ENVIRONMENT=production
APP_BASE_URL=https://yourdomain.com
# OR if in subdirectory:
# APP_BASE_URL=https://yourdomain.com/subdirectory

SERVER=https://yourdomain.com
```

**Important:** Replace `https://yourdomain.com` with your actual live server URL.

### Step 5: Set Correct Permissions
```bash
# On your live server:
chmod 600 .env  # Protect the .env file
chmod 755 config/config.js.php  # Make config readable
chmod 755 uploads  # Ensure uploads directory is accessible
chmod -R 644 uploads/*  # Make uploaded files readable
```

### Step 6: Verify Uploads Directory
Ensure the `uploads` directory and its subdirectories exist:

```bash
# On your live server:
ls -la uploads/
# Should show: requests/, offers/, and user directories (u1/, u2/, etc.)
```

If missing, create them:

```bash
mkdir -p uploads/requests uploads/offers
chmod 755 uploads uploads/requests uploads/offers
```

### Step 7: Test on Live Server
1. Open your live site in a browser
2. Log in as a seller or buyer
3. Navigate to the dashboard
4. Open browser console (F12) and check for:
   - `window.APP_CONFIG` should be defined
   - `window.APP_CONFIG.BASE_PATH` should show your live URL
   - No 404 errors for images

## Troubleshooting

### Images Still Not Showing

**Check 1: Verify APP_CONFIG is loaded**
```javascript
// In browser console:
console.log(window.APP_CONFIG);
// Should output: {BASE_URL: "https://yourdomain.com/", BASE_PATH: "https://yourdomain.com/", ENVIRONMENT: "production"}
```

**Check 2: Verify image paths in database**
Images should be stored as relative paths like `uploads/u1/p2/image.jpg` (without leading `/` or domain).

**Check 3: Check file permissions**
```bash
# On live server:
ls -la uploads/
# All directories should be 755, files should be 644
```

**Check 4: Check .htaccess (if using Apache)**
Ensure `.htaccess` doesn't block access to the uploads directory.

### 404 Errors for config.js.php

If you see `404 Not Found` for `config.js.php`:
1. Verify the file exists: `ls -la config/config.js.php`
2. Check file permissions: `chmod 755 config/config.js.php`
3. Ensure `config/env_loader.php` exists

### Database Image Paths Wrong

If images in the database have full URLs (e.g., `http://localhost:8888/...`):

```sql
-- Update product images
UPDATE products 
SET product_image = REPLACE(product_image, 'http://localhost:8888/register_sample/', '');

-- Update offer images
UPDATE offers 
SET image = REPLACE(image, 'http://localhost:8888/register_sample/', '');

-- Update request images  
UPDATE requests
SET image = REPLACE(image, 'http://localhost:8888/register_sample/', '');
```

## Quick Reference

### Environment Variables
| Variable | Localhost | Production |
|----------|-----------|------------|
| `APP_BASE_URL` | `http://localhost:8888/register_sample` | `https://yourdomain.com` |
| `APP_ENVIRONMENT` | `development` | `production` |
| `PAYSTACK_SECRET_KEY` | `sk_test_...` | `sk_live_...` |
| `PAYSTACK_PUBLIC_KEY` | `pk_test_...` | `pk_live_...` |

### File Permissions
- `.env`: `600` (read/write for owner only)
- `config/config.js.php`: `755` (executable)
- `uploads/`: `755` (readable and executable)
- `uploads/*`: `644` (readable)

## Support

If images still don't show after following these steps:
1. Check browser console for specific error messages
2. Check server error logs: `tail -f /var/log/apache2/error.log` (or nginx equivalent)
3. Verify the exact URL structure of your live server
4. Ensure all files were uploaded correctly

## Rollback

If you need to rollback these changes:

```bash
git revert HEAD
git push origin main
```

Then redeploy the previous version on your live server.
