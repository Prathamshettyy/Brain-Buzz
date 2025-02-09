# Brain Buzz - Quiz Management System  

Brain Buzz is a web-based Quiz Management System designed to facilitate seamless interactions between students and teachers. It enables students to participate in quizzes, track their scores, and view leaderboards while allowing teachers to create, manage, and analyze quizzes.  

## Features  

### Student Features  
- Participate in quizzes and submit responses  
- View leaderboard rankings and track performance  
- Review past quiz results  
- Update account details  

### Teacher Features  
- Create, edit, and delete quizzes  
- View and analyze student performance  
- Manage student accounts  

### Authentication  
- Secure user registration and login system  
- Password reset functionality  

## Technologies Used  

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP  
- **Database:** MySQL  
- **Email Integration:** PHPMailer (for email notifications and password reset)  

## Installation and Setup  

### Prerequisites  
- Install **XAMPP** (if using XAMPP) or **WAMP** (if using WAMP)  
- Ensure **Apache** and **MySQL** services are running  

### Clone the Repository  
```bash
git clone https://github.com/Prathamshettyy/Brain-Buzz.git
```

### Move Project to the Server Directory  

- **For XAMPP:** Move the `Brain-Buzz` folder to  
  ```
  C:\xampp\htdocs\
  ```
- **For WAMP:** Move the `Brain-Buzz` folder to  
  ```
  C:\wamp64\www\
  ```

### Database Setup  

1. Open **phpMyAdmin** by navigating to  
   - **XAMPP:** `http://localhost/phpmyadmin/`  
   - **WAMP:** `http://localhost/phpmyadmin/`  

2. Create a new database named `quiz`.  

3. Import the database file:  
   - Click **Import**  
   - Select `db/quiz.sql`  
   - Click **Go**  

### Configure Database Connection  

- Open the `sql.php` file in the project directory and update the following:  
  ```php
  $host = "localhost";
  $user = "root";  // Default user for both XAMPP and WAMP
  $password = "";  // Default password (leave empty)
  $dbname = "quiz";  // Database name
  ```

### Start the Application  

- Open a browser and navigate to:  
  ```
  http://localhost/Brain-Buzz/
  ```

## License  

This project is open-source and available under the MIT License.  

## Contributing  

Contributions are welcome. Fork the repository, create a new branch, and submit a pull request with enhancements or bug fixes.  
