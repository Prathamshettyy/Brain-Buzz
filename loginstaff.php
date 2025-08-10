<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'sql.php'; // Uses $pdo

$error_message = null;

if (isset($_POST['login_staff'])) {
    try {
        // Find the staff member by their email (or staffid, if you prefer)
        $sql = "SELECT * FROM staff WHERE mail = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['email']]);
        $row = $stmt->fetch();

        // *** THIS IS THE FIX ***
        // Use password_verify() to securely check the password against the stored hash.
        if ($row && password_verify($_POST['pass'], $row['pw'])) {
            // Password is correct, set session variables
            $_SESSION["name"] = $row['name'];
            $_SESSION["staffid"] = $row['staffid'];
            $_SESSION["email"] = $row['mail'];
            $_SESSION["acc_type"] = 'staff';
            header("Location: homestaff.php");
            exit();
        } else {
            $error_message = "Invalid Email or Password.";
        }
    } catch (PDOException $e) {
        $error_message = "A database error occurred.";
        // For debugging: error_log($e->getMessage());
    }
}

include_once 'header.php';
?>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Staff Login</h2>
            <p>Please enter your credentials to access the dashboard.</p>
        </div>
        <?php if ($error_message): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="loginstaff.php" method="post" autocomplete="off">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pass" required>
            </div>
            <button type="submit" name="login_staff" class="btn btn-solid" style="width:100%;">Login</button>
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