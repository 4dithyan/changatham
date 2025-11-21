# Package Booking Website

Welcome to the Package Booking website - a complete travel booking platform for Kerala tourism packages.

## Overview

This is a fully functional travel booking website that allows users to:
- Browse and book Kerala tourism packages
- Manage bookings and payments
- View galleries and reviews
- Contact the travel agency

Administrators can:
- Manage packages, activities, and bookings
- View reports and analytics
- Manage users and content

## Features

### User Features
- User registration and login
- Package browsing with detailed information
- Booking system with payment options
- Gallery viewing
- Review submission
- Notification system
- Profile management

### Admin Features
- Admin dashboard with analytics
- Package management (create, edit, delete)
- Activity management
- Booking management
- User management
- Gallery management
- Report generation
- Notification system

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP 7+
- **Database**: MySQL
- **Additional Libraries**: 
  - Bootstrap Icons
  - Google Fonts (Poppins, Playfair Display)

## Installation

1. Clone or download the repository
2. Place the files in your web server directory (e.g., htdocs for XAMPP)
3. Create a MySQL database named `changatham`
4. Import the database schema (if available)
5. Update `config.php` with your database credentials
6. Access the website through your browser

## Default Admin Credentials

- **Email**: admin@example.com
- **Password**: admin123

*Note: For security reasons, please change the default password after your first login.*

## File Structure

```
changatham/
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── admin_header.php
│   ├── admin_footer.php
│   ├── dashboard_header.php
│   └── common.css
├── images/
├── uploads/
├── aboutus.php
├── add_package.php
├── add_review.php
├── admin_gallery.php
├── admin_package_report.php
├── admin_page.php
├── admin_update_dates.php
├── admin_users.php
├── book_package.php
├── bookings.php
├── cancel_booking.php
├── config.php
├── contact.php
├── explore.php
├── favicon.ico
├── feedback.php
├── gallery.php
├── get_booking_details.php
├── home.php
├── login.php
├── logout.php
├── manage_activities.php
├── manage_bookings.php
├── manage_buses.php
├── manage_drivers.php
├── manage_guides.php
├── manage_packages.php
├── manage_packages_detail.php
├── notifications.php
├── package_detail.php
├── packages.php
├── pay_remaining.php
├── process_payment.php
├── register.php
├── reviews.php
└── send_package_reminders.php
```

## Security Notes

- All passwords are hashed using PHP's `password_hash()` function
- User inputs are sanitized using `mysqli_real_escape_string()`
- Prepared statements are used for database queries where possible
- Session management for user authentication

## Admin login
admin id : admin@changatham.com
pass     : admin123

## Customer login
Register and login.

## Customization

To customize the website:
1. Update the `config.php` file with your database credentials
2. Modify the header and footer files in the `includes/` directory
3. Update images in the `images/` directory
4. Adjust styles in `includes/common.css`

## Support

For any issues or questions, please check the code documentation or contact the development team.
