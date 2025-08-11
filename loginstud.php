<?php
// Start session and include the new PDO connection file
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'sql.php'; // This now creates the $pdo object

$error_message = null;

if (isset($_POST['login_student'])) {
    try {
        // Use prepared statements to find the student by their email
        $sql = "SELECT * FROM student WHERE mail = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['email']]);
        $row = $stmt->fetch(); // Fetch the first matching row

        // *** THIS IS THE FIX ***
        // Use password_verify() to securely check the submitted password against the stored hash
        if ($row && password_verify($_POST['pw'], $row['pw'])) {
            // Password is correct, set session variables for the student
            $_SESSION["name"] = $row['name'];
            $_SESSION["usn"] = $row['usn'];
            $_SESSION["email"] = $row['mail'];
            $_SESSION["acc_type"] = 'student';

            header("Location: homestud.php");
            exit();
        } else {
            $error_message = "Invalid Email or Password.";
        }
    } catch (PDOException $e) {
        // If there's a database error, show a generic message
        $error_message = "A database error occurred. Please try again later.";
        // For debugging: error_log($e->getMessage());
    }
}

// Now we include the new header
include_once 'header.php';
?>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Student Login</h2>
            <p>Please enter your credentials to access the dashboard.</p>
        </div>

        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="loginstud.php" method="post" autocomplete="off">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="pw">Password</label>
                <input type="password" id="pw" name="pw" required>
            </div>
            <button type="submit" name="login_student" class="btn btn-solid" style="width:100%;">Login</button>
             <div class="form-footer-link">
                <a href="forgot-password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</div>

<style>
/* Add some basic styling for the error message */
.message.error {
    background-color: #f8d7da;
    color: #721c24;
    padding: 1rem;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 1rem;
    border: 1px solid #f5c6cb;
}
</style>

<?php include_once 'footer.php'; ?>