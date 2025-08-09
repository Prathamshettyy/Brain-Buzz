<?php
session_start();

if (isset($_POST['login_staff'])) {
    require_once 'sql.php';
    $conn = mysqli_connect($host, $user, $ps, $project);
    if (!$conn) {
        $error_message = "Database error. Please try again later.";
    } else {
        $staffid = mysqli_real_escape_string($conn, $_POST['staffid']);
        $password = mysqli_real_escape_string($conn, $_POST['pass']);
        $sql = "SELECT * FROM staff WHERE staffid='{$staffid}'";
        $res = mysqli_query($conn, $sql);

        if ($row = mysqli_fetch_assoc($res)) {
            if ($password === $row['pw']) {
                $_SESSION["name"] = $row['name'];
                $_SESSION["staffid"] = $row['staffid'];
                $_SESSION["email"] = $row['mail'];
                $_SESSION["acc_type"] = 'staff';
                header("Location: homestaff.php");
                exit();
            } else {
                $error_message = "Invalid Staff ID or Password.";
            }
        } else {
            $error_message = "Invalid Staff ID or Password.";
        }
        mysqli_close($conn);
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

        <?php
        if (isset($error_message)) {
            echo '<p style="color: #f87171; text-align: center; margin-bottom: 1rem;">' . $error_message . '</p>';
        }
        ?>

        <form action="loginstaff.php" method="post" autocomplete="off">
            <div class="form-group">
                <label for="staffid">Staff ID</label>
                <input type="text" id="staffid" name="staffid" required>
            </div>
            <div class="form-group">
                <label for="pass">Password</label>
                <input type="password" id="pass" name="pass" required>
            </div>
            <button type="submit" name="login_staff" class="btn btn-solid" style="width:100%;">Login</button>
            <div class="form-footer-link">
    <a href="forgot-password.php">Forgot Password?</a>
</div>
        </form>
    </div>
</div>

<?php
include_once 'footer.php';
?>