<?php
// This page handles the second step: verifying the OTP and updating the password
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user hasn't started the process, redirect them to the beginning
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
            // --- ✅ Securely hash the new password ---
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $email = $_SESSION['reset_email'];
            $type = $_SESSION['reset_type'];

            // Prepare the SQL statement to update the password
            $sql = "UPDATE `{$type}` SET pw = :password WHERE mail = :email";
            $stmt = $pdo->prepare($sql);

            // Execute the update
            if ($stmt->execute(['password' => $hashed_password, 'email' => $email])) {
                // Success! Destroy the session and redirect to the login page
                session_destroy();
                header("Location: login.php?reset=success");
                exit();
            } else {
                $feedback = ['message' => 'Failed to update your password. Please try again.', 'type' => 'error'];
            }
        }
    } catch (PDOException $e) {
        $feedback = ['message' => 'A database error occurred. Please contact support.', 'type' => 'error'];
    }
}

include_once 'header.php';
?>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Reset Your Password</h2>
            <p>A reset code has been sent to <strong><?php echo htmlspecialchars($_SESSION['reset_email']); ?></strong>. Please enter it below.</p>
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