# Brain-Buzz

A comprehensive web-based quiz management system that facilitates seamless interactions between students and teachers. Built with PHP, featuring dual database support (MySQL for local development, PostgreSQL for production) and modern development practices.

## ✨ Features

### For Students
- **Interactive Quiz Experience**: Participate in quizzes with real-time feedback
- **Performance Tracking**: Comprehensive score tracking and personal analytics
- **Leaderboard System**: View rankings and compete with peers
- **User Management**: Secure registration, login, and profile management
- **Score Cards**: Detailed breakdown of quiz performances

### For Staff/Teachers
- **Staff Account Management**: Complete staff registration and management system
- **Quiz Creation & Management**: Create, edit, and manage quizzes with ease
- **Question Bank**: Add multiple-choice questions with flexible options
- **Student Analytics**: Monitor student performance and progress
- **Leaderboard Administration**: Access to student rankings and statistics
- **Staff Dashboard**: Comprehensive overview of quiz activities

### System Features
- **Dual Database Support**: Automatic environment detection (MySQL/PostgreSQL)
- **Email Integration**: Password recovery and notifications via PHPMailer
- **Responsive Design**: Mobile-friendly interface with improved navigation
- **Environment Variables**: Secure configuration management with dotenv
- **Docker Support**: Containerized deployment with PostgreSQL
- **PDO Database Layer**: Modern, secure database interactions

## 🛠️ Tech Stack

- **Backend**: PHP (95.9%) with PDO for database interactions
- **Styling**: CSS (2.7%) with responsive design
- **Database**: 
  - PostgreSQL/PLpgSQL (1.1%) - Production (Render)
  - MySQL - Local development (WAMP/XAMPP)
- **Email**: PHPMailer with SMTP support
- **Environment Management**: vlucas/phpdotenv for configuration
- **Containerization**: Docker with Apache and PDO extensions
- **Dependency Management**: Composer

## 🚀 Installation

### Local Development Setup (WAMP/XAMPP)

1. **Clone the repository**
   ```bash
   git clone https://github.com/Prathamshettyy/Brain-Buzz.git
   cd Brain-Buzz
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database Setup**
   - Import `db/quiz.sql` into your MySQL database
   - Create a database named `quiz`
   - The system will automatically detect local environment

4. **Environment Configuration** (Optional)
   - Copy `.env.example` to `.env` if you want to use environment variables
   - Configure your email settings for password recovery features

5. **Start Development Server**
   - Start WAMP/XAMPP server
   - Access the application via `http://localhost/Brain-Buzz`

### Production Deployment

The application is configured for automatic deployment on Render with:
- PostgreSQL database integration
- Docker containerization
- Environment variable configuration via `DATABASE_URL`

## 🔧 Configuration

### Database Configuration
The system automatically detects the environment:

- **Local Development**: Uses MySQL connection via traditional credentials
- **Production (Render)**: Uses PostgreSQL via `DATABASE_URL` environment variable

### Email Configuration
PHPMailer is configured to use environment variables:
- `SMTP_HOST`: SMTP server hostname
- `SMTP_USERNAME`: Email account username
- `SMTP_PASSWORD`: Email account password
- `SMTP_PORT`: SMTP port (typically 587 or 465)

## 📁 Current File Structure

```
Brain-Buzz/
├── assets/                 # Static assets (CSS, fonts, images)
├── db/                     # Database schema and migration files
├── PHPMailer/              # Email functionality library
├── vendor/                 # Composer dependencies
├── *.php                   # Core application files
├── Dockerfile              # Container configuration
├── composer.json           # PHP dependencies
└── README.md               # Documentation
```

**Note**: For better organization, consider implementing the improved file structure as outlined in the project documentation.

## 🔑 Key Files & Components

### Core Application
- `index.php` - Landing page and main entry point
- `sql.php` - Database connection handler with environment detection
- `header.php` / `footer.php` - Shared navigation and layout components

### Authentication System
- `signup.php` - Student registration
- `login.php` - General login interface
- `loginstaff.php` / `loginstud.php` - Role-specific login pages
- `forgot-password.php` / `reset-password.php` - Password recovery system
- `add_staff.php` - Staff account creation (New Feature)

### Student Features
- `homestud.php` - Student dashboard
- `takeq.php` - Quiz-taking interface
- `studprofile.php` - Student profile management
- `studscorecard.php` - Performance analytics
- `studleaderboard.php` - Student rankings

### Staff/Admin Features
- `homestaff.php` - Staff dashboard
- `addq.php` / `addqs.php` - Quiz and question creation
- `staffprofile.php` - Staff profile management
- `staffleaderboard.php` - Administrative leaderboard view
- `viewq.php` - Quiz review and management

### Utilities
- `contact.php` - Contact form with email integration
- `quizlist.php` - Quiz listing and selection
- `delete.php` - Content management operations

## 🗄️ Database Schema

### Core Tables
- **student** - Student information and credentials
- **staff** - Teacher/administrator information
- **quiz** - Quiz metadata and configuration
- **questions** - Quiz questions with multiple-choice options
- **score** - Student results and performance tracking
- **dept** - Department/category information

### Features
- PostgreSQL triggers for advanced functionality
- Automatic environment detection for database connections
- Support for both MySQL (local) and PostgreSQL (production)

## 🌐 Live Demo

**🚀 Production URL**: [https://brain-buzz.onrender.com](https://brain-buzz.onrender.com)

### Demo Credentials
- **Staff Login**: Use the staff registration feature or contact administrator
- **Student Login**: Register as a new student or use existing credentials

## 🔄 Recent Updates

### Latest Features (v2.0)
- ✅ **Staff Management System**: Complete staff account creation and management
- ✅ **Enhanced Authentication**: Improved login flow with email-based authentication
- ✅ **PDO Integration**: Migrated from MySQLi to PDO for better security and compatibility
- ✅ **Environment Variables**: Secure configuration management with dotenv support
- ✅ **Email Integration**: PHPMailer with streamlined configuration
- ✅ **Responsive Design**: Enhanced mobile navigation and improved UI
- ✅ **Password Recovery**: Complete forgot/reset password functionality
- ✅ **Docker Support**: Production-ready containerization with PostgreSQL

### Technical Improvements
- Refactored database interactions across all files to use PDO
- Enhanced error handling and security measures
- Improved mobile responsiveness and navigation
- Streamlined PHPMailer configuration with environment variables
- Added Docker support with PHP Apache and PDO extensions
- Implemented proper session management and security

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👨‍💻 Author

**Pratham Shetty**
- GitHub: [@Prathamshettyy](https://github.com/Prathamshettyy)
- Project Link: [https://github.com/Prathamshettyy/Brain-Buzz](https://github.com/Prathamshettyy/Brain-Buzz)

## 🙏 Acknowledgments

- PHP community for excellent documentation and resources
- PHPMailer team for the robust email functionality
- Render platform for hosting and deployment services
- Contributors who have helped improve the codebase

---

**Brain-Buzz** - Empowering education through interactive quiz management ✨
