<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require necessary files
require_once 'sql.php'; // Your PDO DB connection
require __DIR__ . '/vendor/autoload.php'; // Composer's autoload for libraries

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// --- Environment Variable Loading ---
// This section handles loading your secret keys and settings.
// It will only load a .env file if it's not running on Render.
// On Render, it uses the environment variables you set in the dashboard.
if (getenv('RENDER') === false) {
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

$feedback = null; // To store user feedback messages

// Check if the form has been submitted
if (isset($_POST['send_otp'])) {
    try {
        $email = trim($_POST['email']);
        $type = trim($_POST['type']); // 'student' or 'staff'

        // Check if the user exists in the database
        $sql = "SELECT * FROM {$type} WHERE mail = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user_exists = $stmt->fetch();

        if ($user_exists) {
            // User found, generate and store a one-time password (OTP)
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_type'] = $type;

            try {
                // --- PHPMailer Configuration ---
                $mail = new PHPMailer(true);

                // Load SMTP config safely from environment variables
                $smtpHost      = $_ENV['SMTP_HOST']       ?? getenv('SMTP_HOST')       ?? '';
                $smtpUsername  = $_ENV['SMTP_USERNAME']   ?? getenv('SMTP_USERNAME')   ?? '';
                $smtpPassword  = $_ENV['SMTP_PASSWORD']   ?? getenv('SMTP_PASSWORD')   ?? '';
                $smtpPort      = $_ENV['SMTP_PORT']       ?? getenv('SMTP_PORT')       ?? 587;
                $smtpFromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?? $smtpUsername;
                $smtpFromName  = $_ENV['SMTP_FROM_NAME']  ?? getenv('SMTP_FROM_NAME')  ?? 'Brain Buzz Admin';
                $smtpSecure    = $_ENV['SMTP_SECURE']     ?? getenv('SMTP_SECURE')     ?? 'tls';

                // Server settings for PHPMailer
                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUsername;
                $mail->Password   = $smtpPassword;
                $mail->Port       = (int) $smtpPort;
                $mail->SMTPSecure = strtolower($smtpSecure) === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;

                if (empty($smtpFromEmail)) {
                    throw new Exception("Sender email (SMTP_FROM_EMAIL) is not configured.");
                }

                // Recipients
                $mail->setFrom($smtpFromEmail, $smtpFromName);
                $mail->addAddress($email);

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Brain-Buzz Password Reset Code';
                $mail->Body    = "Your one-time password reset code is: <b>{$otp}</b>";

                // Send the email
                $mail->send();

                // Redirect user to the reset page
                header("Location: reset-password.php");
                exit();

            } catch (Exception $e) {
                // Handle email sending errors
                $feedback = [
                    'message' => "Could not send email. Please try again later. Error: " . htmlspecialchars($mail->ErrorInfo),
                    'type'    => 'error'
                ];
            }
        } else {
            // Handle case where user is not found
            $feedback = [
                'message' => 'No account found with that email for the selected user type.',
                'type'    => 'error'
            ];
        }
    } catch (PDOException $e) {
        // Handle database connection errors
        $feedback = [
            'message' => 'A database error occurred. Please contact support.',
            'type'    => 'error'
        ];
    }
}

// Include header file
include_once 'header.php';
?>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Forgot Your Password?</h2>
            <p>Enter your email and select your account type. We'll send you a code to reset it.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo htmlspecialchars($feedback['type']); ?>">
                <?php echo htmlspecialchars($feedback['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="forgot-password.php" autocomplete="off">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="type">Account Type</label>
                <select id="type" name="type" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <button type="submit" name="send_otp" class="btn btn-solid" style="width:100%;">Send Reset Code</button>
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
        background-color: #f8d7da; /* Light red */
        color: #721c24; /* Dark red */
        border: 1px solid #f5c6cb;
    }
    .message.success {
        background-color: #d4edda; /* Light green */
        color: #155724; /* Dark green */
        border: 1px solid #c3e6cb;
    }
</style>

<?php include_once 'footer.php'; ?>