# Brain-Buzz

A web-based quiz management system for seamless interactions between students and teachers.

## Features

### For Students
- Quiz participation with real-time feedback
- Score tracking and leaderboard viewing
- User registration and secure login

### For Staff  
- Quiz creation and management
- Question addition with multiple choice options
- Student performance analytics and leaderboards

## Tech Stack

- **PHP** (95.8%) - Server-side logic
- **CSS** (2.9%) - Styling
- **PostgreSQL/PLpgSQL** (1.2%) - Database (production)
- **MySQL** - Database (local development)
- **Docker** - Containerization

## Installation

### Local Development (WAMP)
1. Clone the repository
```bash
git clone https://github.com/Prathamshettyy/Brain-Buzz.git
```

2. Set up database
- Import `db/quiz.sql` into MySQL
- Database name: `quiz`

3. Start WAMP server and access via localhost

### Production Deployment
- Deployed on [Render](https://brain-buzz.onrender.com/)
- Uses PostgreSQL database
- Automatic deployment via Docker

## Database Configuration

The system automatically detects the environment:
- **Local (WAMP)**: MySQL connection
- **Production (Render)**: PostgreSQL connection via DATABASE_URL

## File Structure

```
Brain-Buzz/
├── assets/          # CSS, fonts, images
├── db/              # Database files
├── PHPMailer/       # Email functionality
├── *.php            # Core application files
├── Dockerfile       # Container configuration
└── README.md
```

## Key Files

- `index.php` - Landing page
- `sql.php` - Database connection handler
- `takeq.php` - Quiz taking interface
- `header.php` - Navigation component
- `db/quiz.sql` - PostgreSQL schema with triggers

## Database Schema

- **student** - Student information
- **staff** - Teacher information  
- **quiz** - Quiz metadata
- **questions** - Quiz questions and answers
- **score** - Student results
- **dept** - Department information

## Live Demo

🚀 [https://brain-buzz.onrender.com](https://brain-buzz.onrender.com)

## Author

**Pratham Shetty** - [GitHub](https://github.com/Prathamshettyy)
