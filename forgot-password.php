<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'sql.php'; // Your PDO connection or DB access

// Load PHPMailer & Dotenv but on Render rely on environment variables directly
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$feedback = null;

if (isset($_POST['send_otp'])) {
    try {
        $email = $_POST['email'];
        $type = $_POST['type']; // 'student' or 'staff'

        $sql = "SELECT * FROM {$type} WHERE mail = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user_exists = $stmt->fetch();

        if ($user_exists) {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_type'] = $type;

            try {
                $mail = new PHPMailer(true);

                $smtpHost      = $_ENV['SMTP_HOST']      ?? getenv('SMTP_HOST')      ?? '';
                $smtpUsername  = $_ENV['SMTP_USERNAME']  ?? getenv('SMTP_USERNAME')  ?? '';
                $smtpPassword  = $_ENV['SMTP_PASSWORD']  ?? getenv('SMTP_PASSWORD')  ?? '';
                $smtpPort      = $_ENV['SMTP_PORT']      ?? getenv('SMTP_PORT')      ?? 587;
                $smtpFromEmail = $_ENV['SMTP_FROM_EMAIL']?? getenv('SMTP_FROM_EMAIL')?? $smtpUsername;
                $smtpFromName  = $_ENV['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME') ?? 'Brain Buzz Admin';
                $smtpSecure    = $_ENV['SMTP_SECURE']    ?? getenv('SMTP_SECURE')    ?? 'tls';

                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUsername;
                $mail->Password   = $smtpPassword;
                $mail->Port       = (int) $smtpPort;
                $mail->SMTPSecure = strtolower($smtpSecure) === 'ssl'
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;

                $mail->setFrom($smtpFromEmail, $smtpFromName);
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Your Brain-Buzz Password Reset Code';
                $mail->Body    = "Your password reset code is: <b>{$otp}</b>";

                $mail->send();

                header("Location: reset-password.php");
                exit();
            } catch (Exception $e) {
                $feedback = ['message' => "Could not send email. Error: " . htmlspecialchars($mail->ErrorInfo), 'type' => 'error'];
            }
        } else {
            $feedback = ['message' => 'No account found with that email for the selected user type.', 'type' => 'error'];
        }
    } catch (PDOException $e) {
        $feedback = ['message' => 'A database error occurred.', 'type' => 'error'];
    }
}

include_once 'header.php';
?>
<!-- Your existing forgot-password HTML and footer here -->

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
</style>

<?php include_once 'footer.php'; ?>
