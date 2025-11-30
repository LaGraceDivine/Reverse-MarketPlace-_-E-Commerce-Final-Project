# Deployment Guide

## Quick Reference

### For GitHub Push
```bash
# Add your GitHub repository as remote
git remote add origin https://github.com/yourusername/your-repo-name.git

# Push to GitHub
git push -u origin master
# or if using main branch:
git branch -M main
git push -u origin main
```

### For Live Server Deployment

1. **Pull from GitHub**
   ```bash
   cd /path/to/your/web/directory
   git clone https://github.com/yourusername/your-repo-name.git
   cd your-repo-name
   ```

2. **Set up environment variables**
   ```bash
   cp .env.example .env
   nano .env  # Edit with production credentials
   ```

3. **Configure .env for production**
   ```
   DB_HOST=your_production_host
   DB_NAME=your_production_database
   DB_USER=your_production_user
   DB_PASSWORD=your_production_password
   
   PAYSTACK_SECRET_KEY=sk_live_your_live_secret_key
   PAYSTACK_PUBLIC_KEY=pk_live_your_live_public_key
   
   APP_ENVIRONMENT=production
   APP_BASE_URL=https://yourdomain.com
   SERVER=https://yourdomain.com
   ```

4. **Set permissions**
   ```bash
   chmod 755 -R .
   chmod 777 uploads/
   chmod 600 .env
   ```

5. **Import database** (if needed)
   ```bash
   mysql -u username -p database_name < your_database_dump.sql
   ```

## Important Security Notes

✅ **What IS committed to Git:**
- All PHP code files
- `.env.example` (template only, no real credentials)
- `.gitignore`
- `README.md`
- All application files

❌ **What is NOT committed to Git:**
- `.env` (contains real credentials)
- `uploads/` directory contents (user files)
- `.DS_Store` and system files
- Log files

## Environment Variable Differences

| Variable | Local Development | Production |
|----------|------------------|------------|
| `DB_HOST` | `localhost` | Your production DB host |
| `DB_NAME` | `dbforlab` | Your production DB name |
| `DB_USER` | `root` | Your production DB user |
| `DB_PASSWORD` | `root` | Your production DB password |
| `PAYSTACK_SECRET_KEY` | `sk_test_...` | `sk_live_...` |
| `PAYSTACK_PUBLIC_KEY` | `pk_test_...` | `pk_live_...` |
| `APP_ENVIRONMENT` | `development` | `production` |
| `APP_BASE_URL` | `http://localhost:8888/register_sample` | `https://yourdomain.com` |

## Testing Before Going Live

1. **Test locally with .env**
   - Verify database connection works
   - Test user registration and login
   - Test payment flow with test keys
   
2. **Test on live server with test keys first**
   - Use `sk_test_...` and `pk_test_...` initially
   - Test complete user flows
   - Verify all features work
   
3. **Switch to live keys only after testing**
   - Update `.env` with `sk_live_...` and `pk_live_...`
   - Test with small real transaction
   - Monitor for any issues

## Updating Live Server from GitHub

When you make changes locally and want to update the live server:

```bash
# On local machine
git add .
git commit -m "Description of changes"
git push origin master

# On live server
cd /path/to/your/web/directory
git pull origin master
# .env file remains unchanged (not overwritten)
```

## Troubleshooting

**Issue: Database connection fails**
- Check `.env` file exists and has correct credentials
- Verify database server is running
- Check database user has proper permissions

**Issue: Paystack payments fail**
- Verify `PAYSTACK_SECRET_KEY` and `PAYSTACK_PUBLIC_KEY` are correct
- Check if using test keys in development, live keys in production
- Verify `APP_BASE_URL` is correct for callback

**Issue: .env not loading**
- Check file permissions: `chmod 600 .env`
- Verify `config/env_loader.php` is being included
- Check for syntax errors in `.env` file
