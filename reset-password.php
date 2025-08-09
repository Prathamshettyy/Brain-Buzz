<?php
// This page handles the second step: verifying OTP and updating the password
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// If the user hasn't been sent an OTP, redirect them to the start of the process
if (!isset($_SESSION['otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

// Include the modern PDO database connection
require_once 'sql.php'; // This creates the $pdo object
$feedback = null;

if (isset($_POST['reset_password'])) {
    try {
        $otp = $_POST['otp'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($otp != $_SESSION['otp']) {
            $feedback = ['message' => 'The reset code is incorrect.', 'type' => 'error'];
        } elseif ($new_password !== $confirm_password) {
            $feedback = ['message' => 'The new passwords do not match.', 'type' => 'error'];
        } else {
            // All good, update the password using a prepared statement
            $email = $_SESSION['reset_email'];
            $type = $_SESSION['reset_type'];
            // In a real application, you should hash the password before saving
            // $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // The table name is dynamic but validated, and columns are fixed
            $sql = "UPDATE {$type} SET pw = ? WHERE mail = ?";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$new_password, $email])) {
                // Password updated, destroy session and redirect to login
                session_destroy();
                header("Location: login.php?reset=success");
                exit();
            } else {
                $feedback = ['message' => 'Failed to update password. Please try again.', 'type' => 'error'];
            }
        }
    } catch (PDOException $e) {
        $feedback = ['message' => 'A database error occurred.', 'type' => 'error'];
    }
}

include_once 'header.php';
?>
<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Reset Your Password</h2>
            <p>An email with a reset code has been sent to <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong>. Please enter it below.</p>
        </div>
        
        <?php if ($feedback): ?>
            <div class="message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
        <?php endif; ?>

        <form method="POST" action="reset-password.php" autocomplete="off">
            <div class="form-group">
                <label for="otp">Reset Code (OTP)</label>
                <input type="text" id="otp" name="otp" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
             <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="reset_password" class="btn btn-solid" style="width:100%;">Update Password</button>
        </form>
    </div>
</div>

<style>
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>
<?php include_once 'footer.php'; ?>