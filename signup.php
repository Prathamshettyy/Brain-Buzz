<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'sql.php'; // Uses the new $pdo connection

$feedback = null;

function handleSignup($pdo, $type) {
    $table = ($type === 'student') ? 'student' : 'staff';
    $is_student = ($type === 'student');
    
    // Define fields based on type
    $fields = ['name', 'email', 'phone', 'dept', 'dob', 'gender', 'password', 'cpassword'];
    // Use different names for unique IDs from the form
    if ($is_student) $fields['id_field'] = 'usn'; else $fields['id_field'] = 'staffid';
    
    $data = [];
    foreach ($fields as $key => $field) {
        if (empty($_POST[$field])) return ['message' => 'All fields are required.', 'type' => 'error'];
        $data[$field] = $_POST[$field];
    }

    if ($data['password'] !== $data['cpassword']) {
        return ['message' => 'Passwords do not match.', 'type' => 'error'];
    }

    // In a real app, hash the password: $hashed_pw = password_hash($data['password'], PASSWORD_DEFAULT);
    
    try {
        if ($is_student) {
            $sql = "INSERT INTO student (usn, name, mail, phno, dept, gender, DOB, pw) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['usn'], $data['name'], $data['email'], $data['phone'], $data['dept'], $data['gender'], $data['dob'], $data['password']]);
        } else {
            $sql = "INSERT INTO staff (staffid, name, mail, phno, dept, gender, DOB, pw) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['staffid'], $data['name'], $data['email'], $data['phone'], $data['dept'], $data['gender'], $data['dob'], $data['password']]);
        }
        return ['message' => ucfirst($type) . ' account created successfully! You can now log in.', 'type' => 'success'];
    } catch (PDOException $e) {
        // Catches errors like duplicate entries
        return ['message' => 'An account with these details may already exist.', 'type' => 'error'];
    }
}

if (isset($_POST['studsu'])) {
    $feedback = handleSignup($pdo, 'student');
}
if (isset($_POST['staffsu'])) {
    $feedback = handleSignup($pdo, 'staff');
}

include_once 'header.php';
?>

<style>
    /* Styles for the tabbed form */
    .tab-nav { display: flex; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
    .tab-link { padding: 1rem 1.5rem; cursor: pointer; font-weight: 500; color: var(--text-secondary); border-bottom: 3px solid transparent; }
    .tab-link.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } }
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.success { background-color: #166534; color: #dcfce7; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>

<div class="container form-container" style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <h2>Join Brain-Buzz</h2>
            <p>Create an account to start your journey with us.</p>
        </div>

        <?php if ($feedback): ?>
            <div class="message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
        <?php endif; ?>

        <nav class="tab-nav">
            <a class="tab-link active" onclick="showTab('student')">I am a Student</a>
            <a class="tab-link" onclick="showTab('staff')">I am Staff</a>
        </nav>

        <div id="student" class="tab-content active">
            <form action="signup.php" method="POST" autocomplete="off">
                <div class="form-grid">
                    <div class="form-group"><label>Full Name</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>USN</label><input type="text" name="usn" required></div>
                    <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Phone Number</label><input type="tel" name="phone" required></div>
                    <div class="form-group"><label>Department</label>
                        <select name="dept" required><option value="CSE">CSE</option><option value="ISE">ISE</option></select>
                    </div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" required></div>
                </div>
                <div class="form-group"><label>Gender</label>
                    <div style="display:flex; gap:1.5rem;"><label><input type="radio" name="gender" value="M" checked> Male</label><label><input type="radio" name="gender" value="F"> Female</label></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <div class="form-group"><label>Confirm Password</label><input type="password" name="cpassword" required></div>
                </div>
                <button type="submit" name="studsu" class="btn btn-solid" style="width:100%;">Create Student Account</button>
            </form>
        </div>

        <div id="staff" class="tab-content">
            <form action="signup.php" method="POST" autocomplete="off">
                 <div class="form-grid">
                    <div class="form-group"><label>Full Name</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Staff ID</label><input type="text" name="staffid" required></div>
                    <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
                    <div class="form-group"><label>Phone Number</label><input type="tel" name="phone" required></div>
                    <div class="form-group"><label>Department</label>
                        <select name="dept" required><option value="CSE">CSE</option><option value="ISE">ISE</option></select>
                    </div>
                     <div class="form-group"><label>Date of Joining</label><input type="date" name="dob" required></div>
                </div>
                <div class="form-group"><label>Gender</label>
                    <div style="display:flex; gap:1.5rem;"><label><input type="radio" name="gender" value="M" checked> Male</label><label><input type="radio" name="gender" value="F"> Female</label></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <div class="form-group"><label>Confirm Password</label><input type="password" name="cpassword" required></div>
                </div>
                <button type="submit" name="staffsu" class="btn btn-solid" style="width:100%;">Create Staff Account</button>
            </form>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
</script>

<?php include_once 'footer.php'; ?>