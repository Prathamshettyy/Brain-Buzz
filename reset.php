<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require necessary files
require_once 'sql.php'; // Your PDO DB connection
require __DIR__ . '/vendor/autoload.php'; // Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// --- Environment Variable Loading ---
// This will only load a .env file if it's not running on Render.
// On Render, it uses the environment variables you set in the dashboard.
if (getenv('RENDER') === false) {
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

$feedback = null; // To store user feedback messages

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    $email = trim($_POST['email1'] ?? '');
    $password = $_POST['pass1'] ?? '';
    $cpassword = $_POST['cpass1'] ?? '';
    $type = trim($_POST['usertype'] ?? '');

    if (empty($email) || empty($password) || empty($cpassword) || empty($type)) {
        $feedback = ['message' => 'All fields are required.', 'type' => 'error'];
    } elseif ($password !== $cpassword) {
        $feedback = ['message' => 'Passwords do not match.', 'type' => 'error'];
    } else {
        try {
            // Check if user exists
            $sql = "SELECT * FROM `{$type}` WHERE mail = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $dbmail = $user['mail'];
                $dbname = $user['name'];

                // Generate a secure one-time password (OTP)
                $otp = mt_rand(100000, 999999);

                try {
                    // --- PHPMailer Configuration ---
                    $mail = new PHPMailer(true);

                    // Load SMTP config safely from environment variables
                    $smtpHost      = $_ENV['SMTP_HOST']       ?? getenv('SMTP_HOST')       ?? '';
                    $smtpUsername  = $_ENV['SMTP_USERNAME']   ?? getenv('SMTP_USERNAME')   ?? '';
                    $smtpPassword  = $_ENV['SMTP_PASSWORD']   ?? getenv('SMTP_PASSWORD')   ?? '';
                    $smtpPort      = $_ENV['SMTP_PORT']       ?? getenv('SMTP_PORT')       ?? 587;
                    $smtpFromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?? $smtpUsername;
                    $smtpFromName  = $_ENV['SMTP_FROM_NAME']  ?? getenv('SMTP_FROM_NAME')  ?? 'Brain Buzz';
                    $smtpSecure    = $_ENV['SMTP_SECURE']     ?? getenv('SMTP_SECURE')     ?? 'tls';

                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = $smtpHost;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpUsername;
                    $mail->Password   = $smtpPassword;
                    $mail->Port       = (int) $smtpPort;
                    $mail->SMTPSecure = strtolower($smtpSecure) === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;

                    // Recipients
                    $mail->setFrom($smtpFromEmail, $smtpFromName);
                    $mail->addAddress($dbmail, $dbname);
                    $mail->addReplyTo($smtpFromEmail);

                    // Email Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset your Brain-Buzz password';
                    $mail->Body    = '<div style="font-family: Arial, sans-serif; line-height: 1.6;">
                                        <h2>Hello ' . htmlspecialchars($dbname) . ',</h2>
                                        <p>Here is your security code to reset your password:</p>
                                        <p style="font-size: 24px; font-weight: bold; color: #042A38;">' . $otp . '</p>
                                        <p>Do not share this security code with anyone.</p>
                                        <hr>
                                        <p>Thank You,<br>The Brain-Buzz Team</p>
                                      </div>';

                    // Send the email
                    $mail->send();

                    // --- Store session data ---
                    $_SESSION['otp']       = $otp;
                    $_SESSION['username']  = $dbmail;
                    // Hash the password securely before storing it in the session
                    $_SESSION['pw']        = password_hash($password, PASSWORD_DEFAULT);
                    $_SESSION['type']      = $type;

                    header("Location: updatepw.php");
                    exit();

                } catch (Exception $e) {
                    $feedback = ['message' => "Could not send email. Error: " . htmlspecialchars($mail->ErrorInfo), 'type' => 'error'];
                }
            } else {
                $feedback = ['message' => 'No account found with that email address for the selected user type.', 'type' => 'error'];
            }
        } catch (PDOException $e) {
            $feedback = ['message' => 'Database error. Please try again later.', 'type' => 'error'];
        }
    }
}
?>

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Reset Your Password</h2>
            <p>Enter your email, new password, and account type to begin.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo htmlspecialchars($feedback['type']); ?>">
                <?php echo htmlspecialchars($feedback['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="rest.php" autocomplete="off">
            <div class="form-group">
                <label for="email1">Email Address</label>
                <input type="email" id="email1" name="email1" required>
            </div>
            <div class="form-group">
                <label for="pass1">New Password</label>
                <input type="password" id="pass1" name="pass1" required>
            </div>
            <div class="form-group">
                <label for="cpass1">Confirm New Password</label>
                <input type="password" id="cpass1" name="cpass1" required>
            </div>
            <div class="form-group">
                <label for="usertype">Account Type</label>
                <select id="usertype" name="usertype" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-solid" style="width:100%;">Send Reset Code</button>
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
    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>