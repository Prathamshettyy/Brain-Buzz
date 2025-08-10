<?php
session_start();
require_once 'sql.php';

// Load Composer's autoloader (PHPMailer installed via Composer or manual files)
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 🔹 If developing locally with a .env file:
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

global $message;

if (isset($_POST['submit'])) {
    if (!empty($_POST['email1']) && !empty($_POST['pass1']) && !empty($_POST['cpass1'])) {

        $conn = mysqli_connect($host, $user, $ps, $project);
        if (!$conn) {
            echo "<script>alert('Database error, please retry later!');</script>";
            exit();
        }

        $type      = mysqli_real_escape_string($conn, $_POST['usertype']);
        $username  = mysqli_real_escape_string($conn, $_POST['email1']);
        $password  = crypt(mysqli_real_escape_string($conn, $_POST['pass1']), 'rakeshmariyaplarrakesh');
        $cpassword = crypt(mysqli_real_escape_string($conn, $_POST['cpass1']), 'rakeshmariyaplarrakesh');

        if ($password === $cpassword) {
            $sql  = "SELECT * FROM `$type` WHERE mail = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $res  = mysqli_stmt_get_result($stmt);

            if ($res && $row = mysqli_fetch_assoc($res)) {
                $dbmail = $row['mail'];
                $dbname = $row['name'];

                if ($dbmail === $username) {
                    $otp = mt_rand(100000, 999999);

                    try {
                        $mail = new PHPMailer(true);

                        // 📌 Load SMTP config from environment variables
                        $smtpHost      = $_ENV['SMTP_HOST']       ?? getenv('SMTP_HOST')       ?? '';
                        $smtpUsername  = $_ENV['SMTP_USERNAME']   ?? getenv('SMTP_USERNAME')   ?? '';
                        $smtpPassword  = $_ENV['SMTP_PASSWORD']   ?? getenv('SMTP_PASSWORD')   ?? '';
                        $smtpPort      = $_ENV['SMTP_PORT']       ?? getenv('SMTP_PORT')       ?? 587;
                        $smtpFromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?? $smtpUsername;
                        $smtpFromName  = $_ENV['SMTP_FROM_NAME']  ?? getenv('SMTP_FROM_NAME')  ?? 'Brain Buzz';
                        $smtpSecure    = $_ENV['SMTP_SECURE']     ?? getenv('SMTP_SECURE')     ?? 'tls';

                        // 📌 PHPMailer config
                        $mail->isSMTP();
                        $mail->Host       = $smtpHost;
                        $mail->SMTPAuth   = true;
                        $mail->Username   = $smtpUsername;
                        $mail->Password   = $smtpPassword;
                        $mail->Port       = (int) $smtpPort;
                        $mail->SMTPSecure = strtolower($smtpSecure) === 'ssl'
                            ? PHPMailer::ENCRYPTION_SMTPS
                            : PHPMailer::ENCRYPTION_STARTTLS;

                        // 📌 Set sender and recipient
                        $mail->setFrom($smtpFromEmail, $smtpFromName);
                        $mail->addAddress($dbmail, $dbname);
                        $mail->addReplyTo($smtpFromEmail);

                        // 📌 Email content
                        $mail->isHTML(true);
                        $mail->Subject = 'Reset your Online Quiz system password';
                        $mail->Body    = '<center><div style="width:100%;background-color:#042A38;color:#fff;">
                                             <h1>Hello ' . htmlspecialchars($dbname) . '</h1>
                                             <br>Here is your security code to reset the password:
                                             <h1>' . $otp . '</h1>
                                             <br>Don\'t share this security code with anyone.
                                             <br><br>Thank You,<br>Online Examination System
                                             <br><a href="mailto:' . $smtpFromEmail . '">Contact Us</a>
                                           </div></center>';

                        $mail->send();

                        // 📌 Store session data
                        $_SESSION['otp']      = $otp;
                        $_SESSION['username'] = $dbmail;
                        $_SESSION['pw']       = $password;
                        $_SESSION['type']     = $type;

                        header("Location: updatepw.php");
                        exit();
                    } catch (Exception $e) {
                        echo "<script>alert('Mailer Error: " . addslashes($mail->ErrorInfo) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Not a registered user — please Sign Up');</script>";
                }
            } else {
                echo "<script>alert('No account found with that email address');</script>";
            }
        } else {
            echo "<script>alert('Both password fields must match');</script>";
        }
    }
}
?>
