# Reverse Marketplace

Africa's first demand-driven marketplace where buyers post what they want and sellers compete to offer the best deals.

## Features

- **For Buyers**: Post requests, review offers, accept deals, and make secure payments
- **For Sellers**: Browse buyer requests, submit competitive offers, fulfill orders
- **For Admins**: Manage users, monitor ratings, handle categories
- **Secure Payments**: Integrated with Paystack for mobile money and card payments
- **Rating System**: Buyers and sellers can rate each other
- **Real-time Chat**: Communication between buyers and sellers

## Tech Stack

- **Backend**: PHP with PDO for database operations
- **Frontend**: HTML, CSS, JavaScript (jQuery)
- **Database**: MySQL
- **Payment Gateway**: Paystack
- **Server**: MAMP (local) / Apache/Nginx (production)

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (optional, for dependencies)
- Paystack account (for payment processing)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd register_sample
   ```

2. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   
   Edit `.env` and update with your local credentials:
   ```
   DB_HOST=localhost
   DB_NAME=dbforlab
   DB_USER=root
   DB_PASSWORD=root
   
   PAYSTACK_SECRET_KEY=sk_test_your_test_key
   PAYSTACK_PUBLIC_KEY=pk_test_your_test_key
   
   APP_ENVIRONMENT=development
   APP_BASE_URL=http://localhost:8888/register_sample
   SERVER=http://localhost:8888
   ```

3. **Set up the database**
   - Create a MySQL database named `dbforlab` (or your preferred name)
   - Import the database schema (if you have a SQL dump file)
   - Update `.env` with your database credentials

4. **Configure MAMP/WAMP/XAMPP**
   - Point your web server to the project directory
   - Ensure PHP and MySQL are running
   - Access the application at `http://localhost:8888/register_sample`

5. **Test the application**
   - Navigate to the homepage
   - Register a new account
   - Test buyer and seller dashboards

### Production/Live Server Deployment

1. **Pull the code to your server**
   ```bash
   git clone <your-repo-url>
   cd register_sample
   ```

2. **Configure environment variables**
   ```bash
   cp .env.example .env
   nano .env  # or use your preferred editor
   ```
   
   Update with production credentials:
   ```
   DB_HOST=your_production_db_host
   DB_NAME=your_production_db_name
   DB_USER=your_production_db_user
   DB_PASSWORD=your_production_db_password
   
   PAYSTACK_SECRET_KEY=sk_live_your_live_key
   PAYSTACK_PUBLIC_KEY=pk_live_your_live_key
   
   APP_ENVIRONMENT=production
   APP_BASE_URL=https://yourdomain.com
   SERVER=https://yourdomain.com
   ```

3. **Set proper file permissions**
   ```bash
   chmod 755 -R .
   chmod 777 uploads/
   chmod 600 .env
   ```

4. **Configure your web server**
   - Point document root to the project directory
   - Ensure `.htaccess` is enabled (for Apache)
   - Configure SSL certificate for HTTPS

5. **Test thoroughly**
   - Test user registration and login
   - Test payment flow with Paystack test mode first
   - Switch to live Paystack keys only after testing

## Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `dbforlab` |
| `DB_USER` | Database username | `root` |
| `DB_PASSWORD` | Database password | `your_password` |
| `PAYSTACK_SECRET_KEY` | Paystack secret key | `sk_test_...` or `sk_live_...` |
| `PAYSTACK_PUBLIC_KEY` | Paystack public key | `pk_test_...` or `pk_live_...` |
| `APP_ENVIRONMENT` | Environment mode | `development` or `production` |
| `APP_BASE_URL` | Full application URL | `http://localhost:8888/register_sample` |
| `SERVER` | Server base URL | `http://localhost:8888` |

## Project Structure

```
register_sample/
├── actions/           # Backend action handlers
├── classes/           # PHP classes (database, models)
├── config/            # Configuration files
│   └── env_loader.php # Environment variable loader
├── controllers/       # Business logic controllers
├── css/               # Stylesheets
├── helpers/           # Helper utilities
├── includes/          # Shared includes
├── js/                # JavaScript files
├── login/             # Authentication and dashboards
├── settings/          # Application settings
├── uploads/           # User uploaded files
├── .env               # Environment variables (gitignored)
├── .env.example       # Environment template
├── .gitignore         # Git ignore rules
└── index.php          # Landing page
```

## User Roles

1. **Buyer (role = 1)**: Can post requests, review offers, make payments
2. **Seller (role = 2)**: Can view requests, submit offers, fulfill orders
3. **Admin (role = 3)**: Can manage users, categories, and monitor the platform

## Security Notes

- Never commit `.env` file to Git (it's in `.gitignore`)
- Use test Paystack keys for development
- Use live Paystack keys only in production
- Keep database credentials secure
- Enable HTTPS in production
- Regularly update dependencies

## Support

For issues or questions, please contact the development team.

## License

Proprietary - All rights reserved
