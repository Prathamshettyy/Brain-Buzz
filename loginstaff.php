<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'sql.php'; // Uses $pdo

$error_message = null;

if (isset($_POST['login_staff'])) {
    try {
        $sql = "SELECT * FROM staff WHERE staffid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['staffid']]);
        $row = $stmt->fetch();

        if ($row && $_POST['pass'] === $row['pw']) {
            $_SESSION["name"] = $row['name'];
            $_SESSION["staffid"] = $row['staffid'];
            $_SESSION["email"] = $row['mail'];
            $_SESSION["acc_type"] = 'staff';
            header("Location: homestaff.php");
            exit();
        } else {
            $error_message = "Invalid Staff ID or Password.";
        }
    } catch (PDOException $e) {
        $error_message = "A database error occurred.";
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
            <p style="color: #f87171; text-align: center; margin-bottom: 1rem;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="loginstaff.php" method="post" autocomplete="off">
            <div class="form-group">
                <label>Staff ID</label>
                <input type="text" name="staffid" required>
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

<?php include_once 'footer.php'; ?>