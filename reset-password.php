<?php
// This page handles the second step: verifying OTP and updating the password
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user hasn't started the process, redirect them.
if (!isset($_SESSION['otp']) || !isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

require_once 'sql.php'; // Your modern PDO database connection
$feedback = null;

if (isset($_POST['reset_password'])) {
    try {
        $otp = $_POST['otp'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($otp != $_SESSION['otp']) {
            $feedback = ['message' => 'The reset code you entered is incorrect.', 'type' => 'error'];
        } elseif ($new_password !== $confirm_password) {
            $feedback = ['message' => 'The new passwords do not match.', 'type' => 'error'];
        } else {
            // Securely hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $email = $_SESSION['reset_email'];
            $type = $_SESSION['reset_type']; // This will be 'student' or 'staff'

            // *** THIS IS THE FIX ***
            // The SQL query is now written without quotes around the table name.
            // This syntax is portable and works on both MySQL and PostgreSQL.
            $sql = "UPDATE {$type} SET pw = ? WHERE mail = ?";
            
            $stmt = $pdo->prepare($sql);

            // Execute the update using an indexed array that matches the "?" placeholders
            if ($stmt->execute([$hashed_password, $email])) {
                // Success! Clean up the session and redirect to the appropriate login page
                session_destroy();
                $success_page = ($type === 'student') ? 'loginstud.php' : 'loginstaff.php';
                header("Location: {$success_page}?reset=success");
                exit();
            } else {
                $feedback = ['message' => 'Failed to update your password. Please try again.', 'type' => 'error'];
            }
        }
    } catch (PDOException $e) {
        // Provide a user-friendly error message for production
        $feedback = ['message' => 'A database error occurred. Please contact support.', 'type' => 'error'];
        // For your own logs, it's helpful to record the actual error
        // On your local machine, you could uncomment the line below to see the real error:
        // $feedback = ['message' => 'Database Error: ' . $e->getMessage(), 'type' => 'error'];
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
            <div class="message <?php echo htmlspecialchars($feedback['type']); ?>">
                <?php echo htmlspecialchars($feedback['message']); ?>
            </div>
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
    .message {
        padding: 1rem;
        border-radius: 6px;
        text-align: center;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }
    .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<?php include_once 'footer.php'; ?>