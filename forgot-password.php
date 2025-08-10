<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'sql.php'; // Your PDO connection or DB access

// Load Composer autoloader (required for PHPMailer)
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables only if .env file exists (useful for local development)
if (getenv('RENDER') === false) {
    if (file_exists(__DIR__ . '/.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

$feedback = null;

if (isset($_POST['send_otp'])) {
    try {
        $email = trim($_POST['email']);
        $type  = trim($_POST['type']); // 'student' or 'staff'

        // Query user existence in DB
        $sql  = "SELECT * FROM {$type} WHERE mail = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user_exists = $stmt->fetch();

        if ($user_exists) {
            $otp = rand(100000, 999999);

            // Store OTP and info in session
            $_SESSION['otp'] = $otp;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_type'] = $type;

            try {
                $mail = new PHPMailer(true);

                // Safely load SMTP config from environment variables
                $smtpHost      = $_ENV['SMTP_HOST']       ?? getenv('SMTP_HOST')       ?? '';
                $smtpUsername  = $_ENV['SMTP_USERNAME']   ?? getenv('SMTP_USERNAME')   ?? '';
                $smtpPassword  = $_ENV['SMTP_PASSWORD']   ?? getenv('SMTP_PASSWORD')   ?? '';
                $smtpPort      = $_ENV['SMTP_PORT']       ?? getenv('SMTP_PORT')       ?? 587;
                $smtpFromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?? $smtpUsername;
                $smtpFromName  = $_ENV['SMTP_FROM_NAME']  ?? getenv('SMTP_FROM_NAME')  ?? 'Brain Buzz Admin';
                $smtpSecure    = $_ENV['SMTP_SECURE']     ?? getenv('SMTP_SECURE')     ?? 'tls';

                if (empty($smtpFromEmail)) {
                    throw new Exception("SMTP_FROM_EMAIL environment variable is missing.");
                }

                // Configure PHPMailer SMTP
                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUsername;
                $mail->Password   = $smtpPassword;
                $mail->Port       = (int) $smtpPort;
                $mail->SMTPSecure = strtolower($smtpSecure) === 'ssl'
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;

                // Set sender and recipient
                $mail->setFrom($smtpFromEmail, $smtpFromName);
                $mail->addAddress($email);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Your Brain-Buzz Password Reset Code';
                $mail->Body    = "Your password reset code is: <b>{$otp}</b>";

                $mail->send();

                header("Location: reset-password.php");
                exit();
            } catch (Exception $e) {
                $feedback = [
                    'message' => "Could not send email. Error: " . htmlspecialchars($mail->ErrorInfo),
                    'type'    => 'error'
                ];
            }
        } else {
            $feedback = [
                'message' => 'No account found with that email for the selected user type.',
                'type'    => 'error'
            ];
        }
    } catch (PDOException $e) {
        $feedback = [
            'message' => 'A database error occurred.',
            'type'    => 'error'
        ];
    }
}

include_once 'header.php';
?>

<!-- Your existing HTML form -->

<div class="container form-container">
    <div class="card">
        <div class="card-header">
            <h2>Forgot Your Password?</h2>
            <p>Enter your email and select your account type. We'll send you a code to reset it.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo $feedback['type']; ?>">
                <?php echo $feedback['message']; ?>
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
    background-color: #991b1b;
    color: #fee2e2;
}
.message.success {
    background-color: #166534;
    color: #dcfce7;
}
</style>

<?php include_once 'footer.php'; ?>
