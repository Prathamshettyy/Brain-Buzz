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
    array_pop($params); // Remove the confirm password

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return ['message' => ucfirst($type) . ' account created successfully! You can now log in.', 'type' => 'success'];
    } catch (PDOException $e) {
        if ($e->getCode() == 23000 || $e->getCode() == 23505) {
             return ['message' => 'An account with this ID or Email may already exist.', 'type' => 'error'];
        }
        return ['message' => 'A database error occurred. Please try again.', 'type' => 'error'];
    }
}

// **FIX:** The order of fields here now correctly matches the database columns
if (isset($_POST['studsu'])) {
    $fields = ['name1', 'usn1', 'mail1', 'phno1', 'dept1', 'dob1', 'gender1', 'password1', 'cpassword1'];
    $sql = "INSERT INTO student (name, usn, mail, phno, dept, DOB, gender, pw) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    $feedback = handleSignup($pdo, 'student', $fields, $sql);
}

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
            <h2>Join Brain-Buzz</h2>
            <p>Create an account to start your journey with us.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo $feedback['type']; ?>"><?php echo htmlspecialchars($feedback['message']); ?></div>
        <?php endif; ?>

        <nav class="tab-nav">
            <a class="tab-link active" onclick="showTab('student')">I am a Student</a>
            <a class="tab-link" onclick="showTab('staff')">I am Staff</a>
        </nav>

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
            </form>
        </div>

        <div id="staff" class="tab-content">
             <form action="signup.php" method="POST" autocomplete="off">
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
</div>
<style>.tab-nav{display:flex;border-bottom:1px solid var(--border-color)}.tab-link{padding:1rem 1.5rem;cursor:pointer;font-weight:500;color:var(--text-secondary);border-bottom:3px solid transparent}.tab-link.active{color:var(--primary-color);border-bottom-color:var(--primary-color)}.tab-content{display:none;padding-top:1.5rem}.tab-content.active{display:block}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem}@media (max-width:768px){.form-grid{grid-template-columns:1fr}}.message{padding:1rem;border-radius:6px;text-align:center;margin-bottom:1.5rem}.message.success{background-color:#166534;color:#dcfce7}.message.error{background-color:#991b1b;color:#fee2e2}</style>
<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
</script>
<?php include_once 'footer.php'; ?>