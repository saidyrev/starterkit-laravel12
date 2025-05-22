# Laravel Admin Starterkit with Sneat Template

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Sneat](https://img.shields.io/badge/Template-Sneat-purple.svg)

A modern, professional Laravel admin starterkit featuring role-based authentication, user management, and a beautiful responsive dashboard built with the Sneat template.

[Features](#features) • [Installation](#installation) • [Usage](#usage) • [Demo](#demo) • [Contributing](#contributing)

</div>

## 🚀 Features

### 🔐 **Authentication & Authorization**

-   **Laravel Breeze** integration with custom Sneat template design
-   **Role-based access control** with permissions system
-   **Beautiful login/register pages** with gradient backgrounds and animations
-   **Password reset functionality** with email notifications
-   **Remember me** and **social login** ready integration

### 👥 **User Management**

-   **Advanced DataTables** with server-side processing
-   **Modal-based CRUD** operations (Create, Read, Update, Delete)
-   **Bulk actions** (delete, activate, deactivate multiple users)
-   **User status management** (active/inactive)
-   **Password reset** for users by admin
-   **Avatar upload** with image optimization
-   **Advanced filtering** (by role, status, date range)
-   **Export functionality** (CSV format)
-   **Real-time search** and pagination

### 🛡️ **Role & Permission Management**

-   **Complete role management** with permissions assignment
-   **Permission-based access control** throughout the application
-   **Role cloning** functionality
-   **Visual permissions grid** for easy management
-   **Bulk role operations**
-   **Auto-save drafts** for forms
-   **Permission search** functionality

### 📊 **Dashboard & Analytics**

-   **Interactive dashboard** with statistics cards
-   **User growth charts** (Chart.js integration)
-   **Role distribution** pie charts
-   **Recent activity** feed
-   **Quick actions** panel
-   **Responsive statistics** widgets

### 👤 **Profile Management**

-   **Complete profile editing** with avatar upload
-   **Password change** functionality
-   **Account information** display
-   **Profile statistics** and activity history
-   **Account deletion** with confirmation

### 🎨 **UI/UX Features**

-   **Sneat Template** integration - modern and professional design
-   **Fully responsive** design (mobile-first approach)
-   **SweetAlert2** integration for beautiful notifications
-   **Loading states** and animations
-   **Dark/Light mode** support (template ready)
-   **Professional gradients** and hover effects
-   **Smooth transitions** and micro-interactions

### 🔧 **Technical Features**

-   **Yajra DataTables** for advanced table functionality
-   **Server-side processing** for optimal performance
-   **AJAX-based operations** for seamless user experience
-   **Real-time form validation** with error handling
-   **File upload** capabilities with validation
-   **Export functionality** (CSV, JSON)
-   **Search and filtering** capabilities
-   **Pagination** with customizable page sizes

## 📋 Requirements

-   **PHP** >= 8.2
-   **Composer** >= 2.0
-   **Node.js** >= 18.x
-   **NPM** >= 9.x
-   **MySQL** >= 8.0 or **PostgreSQL** >= 13
-   **Web Server** (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-admin-starterkit.git
cd laravel-admin-starterkit
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Configuration

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Seed the database with default roles, permissions, and admin user
php artisan db:seed
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Compile Assets

```bash
# For development
npm run dev

# For production
npm run build
```

### 9. Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 🔑 Default Login Credentials

After running the seeders, you can login with:

-   **Email**: `admin@example.com`
-   **Password**: `password`
-   **Role**: Administrator (full access)

> ⚠️ **Important**: Change these credentials immediately after installation!

## 📁 Project Structure

```
laravel-admin-starterkit/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── UserController.php
│   │   ├── RoleController.php
│   │   └── ProfileController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Permission.php
│   └── Helpers/
│       └── SweetAlert.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── RolePermissionSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── sneat.blade.php
│   │   │   └── auth.blade.php
│   │   ├── auth/
│   │   ├── users/
│   │   ├── roles/
│   │   └── profile/
│   └── css/
├── public/
│   ├── sneat/          # Sneat template assets
│   └── css/            # Custom CSS files
└── routes/
    └── web.php
```

## ⚙️ Configuration

### Role & Permission System

The application uses a simple but powerful role-permission system:

1. **Roles** - Define user roles (admin, editor, user, etc.)
2. **Permissions** - Define specific permissions (manage_users, manage_roles, etc.)
3. **Assignment** - Assign permissions to roles, and roles to users

### Default Permissions

-   `view_dashboard` - Access to dashboard
-   `manage_users` - Full user management access
-   `manage_roles` - Full role management access
-   `view_reports` - Access to reports
-   `create_content` - Create new content
-   `edit_content` - Edit existing content
-   `delete_content` - Delete content

### Adding New Permissions

1. Add permission to the `RolePermissionSeeder.php`
2. Run `php artisan db:seed --class=RolePermissionSeeder`
3. Use in your controllers: `auth()->user()->hasPermission('permission_name')`
4. Use in Blade templates: `@if(auth()->user()->hasPermission('permission_name'))`

## 🎯 Usage

### User Management

Navigate to **Users Management** to:

-   View all users in an advanced DataTable
-   Add new users with role assignment
-   Edit existing users and change their roles
-   Activate/deactivate user accounts
-   Reset user passwords
-   Delete users (with protection)
-   Export users to CSV
-   Use bulk actions for multiple users

### Role Management

Navigate to **Roles & Permissions** to:

-   Create new roles with specific permissions
-   Edit existing roles and modify permissions
-   Clone roles for quick setup
-   View role details and assigned users
-   Delete unused roles
-   Export roles data

### Dashboard

The dashboard provides:

-   User statistics and growth charts
-   Recent user registrations
-   Quick action buttons
-   System overview cards
-   Activity feed

## 🎨 Customization

### Changing Colors

The application uses CSS custom properties for easy theming:

```css
/* In public/css/custom.css */
:root {
    --primary-color: #696cff;
    --secondary-color: #9155fd;
    --success-color: #28c76f;
    --danger-color: #ff3e1d;
    --warning-color: #ffab00;
}
```

### Adding New Pages

1. Create controller: `php artisan make:controller YourController`
2. Create views in `resources/views/`
3. Add routes in `routes/web.php`
4. Add navigation links in `resources/views/layouts/partials/sidebar.blade.php`

### Custom Permissions

Add new permissions in `database/seeders/RolePermissionSeeder.php`:

```php
$permissions = [
    // ... existing permissions
    ['name' => 'your_permission', 'display_name' => 'Your Permission'],
];
```

## 🔧 API Endpoints

The application includes API endpoints for:

-   User validation
-   Role validation
-   Export functionality
-   AJAX operations

All API endpoints are protected with authentication and CSRF tokens.

## 📱 Mobile Support

The application is fully responsive and includes:

-   Mobile-optimized DataTables
-   Touch-friendly buttons and controls
-   Responsive navigation
-   Mobile-specific layouts
-   Progressive Web App (PWA) ready

## 🧪 Testing

```bash
# Run PHPUnit tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Generate test coverage report
php artisan test --coverage
```

## 📊 Performance

### Optimization Features

-   **Server-side processing** for DataTables
-   **Lazy loading** for large datasets
-   **Image optimization** for avatars
-   **Asset bundling** with Vite
-   **Database indexing** for queries
-   **Caching** for roles and permissions

### Performance Tips

-   Use `php artisan optimize` for production
-   Enable OPcache in production
-   Use Redis for session and cache storage
-   Configure database connection pooling

## 🔒 Security

### Security Features

-   **CSRF Protection** on all forms
-   **XSS Protection** with Blade templates
-   **SQL Injection** prevention with Eloquent ORM
-   **Password hashing** with bcrypt
-   **Rate limiting** on login attempts
-   **Input validation** and sanitization

### Security Best Practices

-   Always validate user input
-   Use permissions for route protection
-   Regularly update dependencies
-   Monitor authentication logs
-   Use HTTPS in production

## 🚀 Deployment

### Production Setup

1. **Environment Configuration**

```bash
APP_ENV=production
APP_DEBUG=false
```

2. **Optimize for Production**

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

3. **Set Proper Permissions**

```bash
chmod -R 755 storage bootstrap/cache
```

### Docker Support

```dockerfile
# Dockerfile included for containerized deployment
FROM php:8.2-fpm
# ... (Docker configuration)
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

-   Follow **PSR-12** coding standards
-   Use **meaningful variable** and method names
-   Add **comments** for complex logic
-   Write **tests** for new features
-   Update **documentation** when needed

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Credits

### Built With

-   **[Laravel](https://laravel.com)** - The PHP Framework for Web Artisans
-   **[Sneat Template](https://themeselection.com/item/sneat-bootstrap-html-admin-template/)** - Bootstrap Admin Template
-   **[Chart.js](https://www.chartjs.org/)** - Simple yet flexible JavaScript charting
-   **[DataTables](https://datatables.net/)** - Advanced table plugin for jQuery
-   **[SweetAlert2](https://sweetalert2.github.io/)** - Beautiful, responsive, customizable popup boxes

### Special Thanks

-   **ThemeSelection** for the amazing Sneat template
-   **Laravel Community** for the excellent framework and ecosystem
-   **Open Source Contributors** who make projects like this possible

## 📞 Support

If you encounter any issues or need help:

1. **Check the Documentation** - Most common issues are covered here
2. **Search Issues** - Someone might have faced the same problem
3. **Create an Issue** - If you found a bug or have a feature request
4. **Discussions** - For general questions and community support

## 🗺️ Roadmap

### Upcoming Features

-   [ ] **Two-Factor Authentication** (2FA)
-   [ ] **Activity Logging** system
-   [ ] **Email Templates** management
-   [ ] **System Settings** panel
-   [ ] **Backup Management** functionality
-   [ ] **Multi-language** support
-   [ ] **Advanced Reporting** module
-   [ ] **API Documentation** with Swagger
-   [ ] **Real-time Notifications** with WebSockets
-   [ ] **File Manager** integration

### Version History

-   **v1.0.0** - Initial release with core features
-   **v1.1.0** - Enhanced role management and bulk actions
-   **v1.2.0** - Profile management and avatar upload
-   **v2.0.0** - Complete UI overhaul with Sneat template

---

<div align="center">

**⭐ If you find this project helpful, please consider giving it a star!**

Made with ❤️ by [Alternativvenesia](https://github.com/saidyrev)

</div>
