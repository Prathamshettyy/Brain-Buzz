<?php
// Start session and handle form logic at the top
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$feedback = null;
$name = $email = $message = ''; // Initialize variables

// Load environment variables from $_ENV or getenv()
// (If you're using vlucas/phpdotenv, load it earlier in your bootstrap)
$smtpHost = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$smtpPort = getenv('SMTP_PORT') ?: 465;
$smtpSecure = getenv('SMTP_SECURE') ?: 'ssl'; // or 'tls'
$smtpUser = getenv('SMTP_USERNAME') ?: null;
$smtpPass = getenv('SMTP_PASSWORD') ?: null;
$recipientEmail = getenv('RECIPIENT_EMAIL') ?: 'prathamshetty329@gmail.com';
$recipientName  = getenv('RECIPIENT_NAME') ?: 'Brain Buzz Admin';

if (isset($_POST["submit"])) {
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $message = trim($_POST["message"] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $feedback = ['message' => 'All fields are required.', 'type' => 'error'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback = ['message' => 'Please enter a valid email address.', 'type' => 'error'];
    } else {
        // Safety: ensure SMTP credentials are available
        if (empty($smtpUser) || empty($smtpPass)) {
            $feedback = ['message' => 'Mail server is not configured. Contact the site administrator.', 'type' => 'error'];
        } else {
            $mail = new PHPMailer(true);
            try {
                // --- SMTP server configuration (from env) ---
                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUser;
                $mail->Password   = $smtpPass;
                // map common values to PHPMailer constants
                if (strtolower($smtpSecure) === 'tls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }
                $mail->Port       = (int) $smtpPort;

                // --- RECIPIENTS ---
                $mail->setFrom($email, htmlspecialchars($name));
                $mail->addAddress($recipientEmail, $recipientName);

                // --- CONTENT ---
                $mail->isHTML(true);
                $mail->Subject = 'New Contact Form Message from ' . htmlspecialchars($name);
                $mail->Body    = "<b>Name:</b> " . htmlspecialchars($name) . "<br>" .
                               "<b>Email:</b> " . htmlspecialchars($email) . "<br><br>" .
                               "<b>Message:</b><br>" . nl2br(htmlspecialchars($message));

                $mail->send();
                $feedback = ['message' => 'Your message has been sent successfully!', 'type' => 'success'];
                $name = $email = $message = '';
            } catch (Exception $e) {
                // Do not echo $e->getMessage() to users in production
                $feedback = ['message' => 'Message could not be sent. Please contact the site admin.', 'type' => 'error'];
            }
        }
    }
}

include_once 'header.php';
?>

<div class="container">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h2><i class="fa fa-address-card"></i> Get in Touch</h2>
        <p style="color:var(--text-secondary);">We'd love to hear from you. Choose an option below or send us a message.</p>
    </div>

    <!-- Quick Contact Action Cards -->
    <div class="contact-actions-grid">
         <a href="tel:+919480242018" class="card-link">
            <div class="action-card"><i class="fa fa-phone"></i><h3>Call Us</h3><p>+91 94802 42018</p></div>
        </a>
        <a href="https://wa.me/919480242018?text=Message%20From%20Brain-Buzz" target="_blank" class="card-link">
            <div class="action-card"><i class="fab fa-whatsapp"></i><h3>WhatsApp</h3><p>Chat with us directly</p></div>
        </a>
        <a href="mailto:<?php echo htmlspecialchars($recipientEmail); ?>" class="card-link">
             <div class="action-card"><i class="fa fa-envelope"></i><h3>Email Us</h3><p><?php echo htmlspecialchars($recipientEmail); ?></p></div>
        </a>
    </div>

    <!-- Contact Form Card -->
    <div class="form-container" style="max-width: 800px; margin-top: 2rem;">
        <div class="card">
             <div class="card-header"><h3>Or Send a Message Directly</h3></div>
            
            <?php if ($feedback): ?>
                <div class="message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
            <?php endif; ?>

            <form action="contact.php" method="post" autocomplete="off">
                <div class="form-group"><label for="name">Your Name</label><input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>"></div>
                <div class="form-group"><label for="email">Your Email</label><input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>"></div>
                <div class="form-group"><label for="message">Your Message</label><textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea></div>
                <button type="submit" name="submit" class="btn btn-solid" style="width:100%;">Send Message</button>
            </form>
        </div>
    </div>
</div>

<style>
    .contact-actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
    .action-card { background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; height: 100%; }
    .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.25); border-color: var(--primary-color); }
    .action-card i { font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem; }
    .action-card h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--text-primary); }
    .action-card p { color: var(--text-secondary); font-size: 0.9rem; }
    a.card-link { text-decoration: none; }
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.success { background-color: #166534; color: #dcfce7; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>

<?php include_once 'footer.php'; ?>
