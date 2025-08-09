<?php
// Start session and include the new PDO connection file
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'sql.php'; // This now creates the $pdo object

$error_message = null;

if (isset($_POST['login_student'])) {
    try {
        // Use prepared statements for security
        $sql = "SELECT * FROM student WHERE usn = ?";
        $stmt = $pdo->prepare($sql);
        
        // The execute method automatically handles sanitizing the input
        $stmt->execute([$_POST['usn']]); 
        
        $row = $stmt->fetch(); // Fetch the first matching row

        if ($row) {
            // For production, you should use password_verify()
            if ($_POST['pw'] === $row['pw']) {
                // Set session variables for the student
                $_SESSION["name"] = $row['name'];
                $_SESSION["usn"] = $row['usn'];
                $_SESSION["email"] = $row['mail'];
                $_SESSION["acc_type"] = 'student';

                header("Location: homestud.php");
                exit();
            } else {
                $error_message = "Invalid USN or Password.";
            }
        } else {
            $error_message = "Invalid USN or Password.";
        }
    } catch (PDOException $e) {
        // If there's a database error, show a generic message
        $error_message = "A database error occurred. Please try again later.";
        // Optional: log the actual error for debugging: error_log($e->getMessage());
    }
}

// Now we include the new header
include_once 'header.php';
?>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Student Login</h2>
            <p>Welcome back! Enter your USN to begin.</p>
        </div>

        <?php
        if (isset($error_message)) {
            echo '<p style="color: #f87171; text-align: center; margin-bottom: 1rem;">' . htmlspecialchars($error_message) . '</p>';
        }
        ?>

        <form action="loginstud.php" method="post" autocomplete="off">
            <div class="form-group">
                <label for="usn">USN (University Seat Number)</label>
                <input type="text" id="usn" name="usn" required>
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

<?php
include_once 'footer.php';
?>