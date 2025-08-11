<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'sql.php'; // This creates the $pdo object

$feedback = null;

function handleSignup($pdo, $type, $fields, $sql) {
    $params = [];
    foreach ($fields as $field) {
        if (!isset($_POST[$field])) {
            return ['message' => 'A required field was missing. Please fill out the entire form.', 'type' => 'error'];
        }
        $params[] = $_POST[$field];
    }

    if ($params[count($params) - 2] !== $params[count($params) - 1]) {
        return ['message' => 'Passwords do not match. Please try again.', 'type' => 'error'];
    }

    $plain_password = $params[count($params) - 2];
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
    $params[count($params) - 2] = $hashed_password;
    
    array_pop($params); // Remove the confirm password

    try {
        // This SQL uses no quotes, so it works on both MySQL and PostgreSQL
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $success_page = ($type === 'student') ? 'loginstud.php' : 'loginstaff.php';
        header("Location: {$success_page}?registration=success");
        exit();

    } catch (PDOException $e) {
        // *** THIS IS THE FIX ***
        // Check for the standard SQLSTATE code for a unique constraint violation.
        // '23000' is the general class, and '23505' is specific to PostgreSQL.
        // This check will work for both your local server and Render.
        if ($e->getCode() == '23000' || $e->getCode() == '23505') { 
             return ['message' => 'An account with this ID or Email already exists. Please try logging in.', 'type' => 'error'];
        }
        // For any other database error, show a generic message.
        return ['message' => 'A database error occurred. Please try again.', 'type' => 'error'];
    }
}

// Handle Student Signup
if (isset($_POST['studsu'])) {
    $fields = ['name1', 'usn1', 'mail1', 'phno1', 'dept1', 'dob1', 'gender1', 'password1', 'cpassword1'];
    $sql = "INSERT INTO student (name, usn, mail, phno, dept, DOB, gender, pw) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    $feedback = handleSignup($pdo, 'student', $fields, $sql);
}

// Handle Staff Signup - This part is only active if you add the staff form back in
if (isset($_POST['staffsu'])) {
    $fields = ['name2', 'staffid', 'mail2', 'phno2', 'dept2', 'dob2', 'gender2', 'password2', 'cpassword2'];
    $sql = "INSERT INTO staff (name, staffid, mail, phno, dept, DOB, gender, pw) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    $feedback = handleSignup($pdo, 'staff', $fields, $sql);
}

include_once 'header.php';
?>

<div class="container form-container" style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <h2>Create a Student Account</h2>
            <p>Join Brain-Buzz to start taking quizzes.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo htmlspecialchars($feedback['type']); ?>"><?php echo htmlspecialchars($feedback['message']); ?></div>
        <?php endif; ?>

        <div id="student" class="tab-content active">
            <form action="signup.php" method="POST" autocomplete="off">
                <div class="form-grid">
                    <div class="form-group"><label>Full Name</label><input type="text" name="name1" required></div>
                    <div class="form-group"><label>USN</label><input type="text" name="usn1" required></div>
                    <div class="form-group"><label>Email Address</label><input type="email" name="mail1" required></div>
                    <div class="form-group"><label>Phone Number</label><input type="tel" name="phno1" pattern="[6789][0-9]{9}" required></div>
                    <div class="form-group"><label>Department</label><select name="dept1" required><option value="CSE">CSE</option><option value="ISE">ISE</option><option value="ECE">ECE</option><option value="EEE">EEE</option></select></div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" name="dob1" required></div>
                </div>
                <div class="form-group"><label>Gender</label><div style="display:flex; gap:1.5rem;"><label><input type="radio" name="gender1" value="M" checked> Male</label><label><input type="radio" name="gender1" value="F"> Female</label></div></div>
                <div class="form-grid">
                    <div class="form-group"><label>Password</label><input type="password" name="password1" required></div>
                    <div class="form-group"><label>Confirm Password</label><input type="password" name="cpassword1" required></div>
                </div>
                <button type="submit" name="studsu" class="btn btn-solid" style="width:100%;">Create Student Account</button>
                <div class="form-footer">
                    <span>Already have an account? <a href="loginstud.php">Login here</a>.</span>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tab-content.active{display:block; padding-top:1.5rem;}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}
    @media (max-width:768px){.form-grid{grid-template-columns:1fr}}
    .message{padding:1rem;border-radius:6px;text-align:center;margin-bottom:1.5rem}
    .message.success{background-color:#166534;color:#dcfce7}
    .message.error{background-color:#991b1b;color:#fee2e2}
    .form-footer {text-align: center; margin-top: 1.5rem;}
    .form-footer a {color: #007bff; text-decoration: none; font-weight: 600;}
</style>

<?php include_once 'footer.php'; ?>