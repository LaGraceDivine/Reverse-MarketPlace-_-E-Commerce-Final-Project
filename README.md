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

**Test the application**
   - Navigate to the homepage
   - Register a new account
   - Test buyer and seller dashboards

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

## Support

For issues or questions, please contact the development team.

## License

Proprietary - All rights reserved
