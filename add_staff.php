<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Authorization check: Ensure only logged-in staff can access this page
if (!isset($_SESSION['acc_type']) || $_SESSION['acc_type'] !== 'staff') {
    header("Location: loginstaff.php?error=unauthorized");
    exit();
}

require_once 'sql.php';
$feedback = null;

// Handle the form submission to add a new staff member
if (isset($_POST['staffsu'])) {
    $name = trim($_POST['name2']);
    $staffid = trim($_POST['staffid']);
    $email = trim($_POST['mail2']);
    $password = $_POST['password2'];
    $confirm_password = $_POST['cpassword2'];

    if ($password !== $confirm_password) {
        $feedback = ['message' => 'Passwords do not match.', 'type' => 'error'];
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // This SQL works for both MySQL and PostgreSQL
            $sql = "INSERT INTO staff (name, staffid, mail, phno, dept, DOB, gender, pw) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                $name, $staffid, $email, 
                $_POST['phno2'], $_POST['dept2'], $_POST['dob2'], 
                $_POST['gender2'], $hashed_password
            ]);
            
            $feedback = ['message' => 'New staff account created successfully!', 'type' => 'success'];

        } catch (PDOException $e) {
            // *** THIS IS THE FIX ***
            // Check for the standard 'unique constraint violation' error code.
            // This works for both duplicate Staff IDs and duplicate Emails.
            if ($e->getCode() == '23000' || $e->getCode() == '23505') {
                $feedback = ['message' => 'An account with this Staff ID or Email already exists.', 'type' => 'error'];
            } else {
                $feedback = ['message' => 'A database error occurred. Please try again.', 'type' => 'error'];
            }
        }
    }
}

include_once 'header.php';
?>

<div class="container form-container" style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <h2>Add New Staff Member</h2>
            <p>Create a new account for a staff member.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo htmlspecialchars($feedback['type']); ?>"><?php echo htmlspecialchars($feedback['message']); ?></div>
        <?php endif; ?>

        <form action="add_staff.php" method="POST" autocomplete="off">
            <div class="form-grid">
                <div class="form-group"><label>Full Name</label><input type="text" name="name2" required></div>
                <div class="form-group"><label>Staff ID</label><input type="text" name="staffid" required></div>
                <div class="form-group"><label>Email Address</label><input type="email" name="mail2" required></div>
                <div class="form-group"><label>Phone Number</label><input type="tel" name="phno2" pattern="[6789][0-9]{9}" required></div>
                <div class="form-group"><label>Department</label><select name="dept2" required><option value="CSE">CSE</option><option value="ISE">ISE</option><option value="ECE">ECE</option><option value="EEE">EEE</option></select></div>
                <div class="form-group"><label>Date of Joining</label><input type="date" name="dob2" required></div>
            </div>
            <div class="form-group"><label>Gender</label><div style="display:flex; gap:1.5rem;"><label><input type="radio" name="gender2" value="M" checked> Male</label><label><input type="radio" name="gender2" value="F"> Female</label></div></div>
            <div class="form-grid">
                <div class="form-group"><label>Password</label><input type="password" name="password2" required></div>
                <div class="form-group"><label>Confirm Password</label><input type="password" name="cpassword2" required></div>
            </div>
            <button type="submit" name="staffsu" class="btn btn-solid" style="width:100%;">Create Staff Account</button>
        </form>
    </div>
</div>

<style>.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}@media (max-width:768px){.form-grid{grid-template-columns:1fr}}.message{padding:1rem;border-radius:6px;text-align:center;margin-bottom:1.5rem}.message.success{background-color:#166534;color:#dcfce7}.message.error{background-color:#991b1b;color:#fee2e2}</style>

<?php include_once 'footer.php'; ?>