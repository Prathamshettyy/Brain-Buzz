<?php
session_start();
require_once 'sql.php';

$message = '';
$message_type = '';

if (isset($_POST['staffsu'])) {
    $conn = mysqli_connect($host, $user, $ps, $project);
    if (!$conn) {
        $message = "Database connection failed.";
        $message_type = 'error';
    } else {
        $fields = ['name2', 'staffid', 'mail2', 'phno2', 'dept2', 'dob2', 'gender2', 'password2', 'cpassword2'];
        $staff_data = [];
        $all_fields_present = true;
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $staff_data[$field] = mysqli_real_escape_string($conn, $_POST[$field]);
            } else {
                $all_fields_present = false;
                break;
            }
        }
        if ($all_fields_present) {
            if ($staff_data['password2'] !== $staff_data['cpassword2']) {
                $message = "Passwords do not match.";
                $message_type = 'error';
            } else {
                $sql = "INSERT INTO staff (staffid, name, mail, phno, dept, gender, DOB, pw) VALUES('{$staff_data['staffid']}', '{$staff_data['name2']}', '{$staff_data['mail2']}', '{$staff_data['phno2']}', '{$staff_data['dept2']}', '{$staff_data['gender2']}', '{$staff_data['dob2']}', '{$staff_data['password2']}')";
                if (mysqli_query($conn, $sql)) {
                    $message = "Staff account created successfully! You can now log in.";
                    $message_type = 'success';
                } else {
                    $message = "An account with this Staff ID or Email may already exist.";
                    $message_type = 'error';
                }
            }
        }
        mysqli_close($conn);
    }
}

if (isset($_POST['studsu'])) {
    $conn = mysqli_connect($host, $user, $ps, $project);
    if (!$conn) {
        $message = "Database connection failed.";
        $message_type = 'error';
    } else {
        $fields = ['name1', 'usn1', 'mail1', 'phno1', 'dept1', 'dob1', 'gender1', 'password1', 'cpassword1'];
        $student_data = [];
        $all_fields_present = true;
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $student_data[$field] = mysqli_real_escape_string($conn, $_POST[$field]);
            } else {
                $all_fields_present = false;
                break;
            }
        }
        if ($all_fields_present) {
            if ($student_data['password1'] !== $student_data['cpassword1']) {
                $message = "Passwords do not match.";
                $message_type = 'error';
            } else {
                $sql = "INSERT INTO student (usn, name, mail, phno, dept, gender, DOB, pw) VALUES('{$student_data['usn1']}', '{$student_data['name1']}', '{$student_data['mail1']}', '{$student_data['phno1']}', '{$student_data['dept1']}', '{$student_data['gender1']}', '{$student_data['dob1']}', '{$student_data['password1']}')";
                if (mysqli_query($conn, $sql)) {
                    $message = "Student account created successfully! You can now log in.";
                    $message_type = 'success';
                } else {
                    $message = "An account with this USN or Email may already exist.";
                    $message_type = 'error';
                }
            }
        }
        mysqli_close($conn);
    }
}

include_once 'header.php';
?>

<style>
    .tab-nav { display: flex; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
    .tab-link { padding: 1rem 1.5rem; cursor: pointer; font-weight: 500; color: var(--text-secondary); border-bottom: 3px solid transparent; transition: color 0.3s ease, border-color 0.3s ease; }
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

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <nav class="tab-nav">
            <a class="tab-link active" onclick="showTab('student')">I am a Student</a>
            <a class="tab-link" onclick="showTab('staff')">I am Staff</a>
        </nav>

        <div id="student" class="tab-content active">
            <form action="signup.php" method="POST" autocomplete="off">
                <div class="form-grid">
                    <div class="form-group"><label for="name1">Full Name</label><input type="text" name="name1" required></div>
                    <div class="form-group"><label for="usn1">USN</label><input type="text" name="usn1" required></div>
                    <div class="form-group"><label for="mail1">Email Address</label><input type="email" name="mail1" required></div>
                    <div class="form-group"><label for="phno1">Phone Number</label><input type="tel" name="phno1" pattern="[6789][0-9]{9}" required></div>
                    <div class="form-group"><label for="dept1">Department</label>
                        <select name="dept1" required><option value="CSE">CSE</option><option value="ISE">ISE</option><option value="ECE">ECE</option><option value="EEE">EEE</option></select>
                    </div>
                    <div class="form-group"><label for="dob1">Date of Birth</label><input type="date" name="dob1" required></div>
                </div>
                <div class="form-group"><label>Gender</label>
                    <div style="display:flex; gap:1.5rem;"><label><input type="radio" name="gender1" value="M" checked> Male</label><label><input type="radio" name="gender1" value="F"> Female</label></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label for="password1">Password</label><input type="password" name="password1" required></div>
                    <div class="form-group"><label for="cpassword1">Confirm Password</label><input type="password" name="cpassword1" required></div>
                </div>
                <button type="submit" name="studsu" class="btn btn-solid" style="width:100%;">Create Student Account</button>
            </form>
        </div>

        <div id="staff" class="tab-content">
            <form action="signup.php" method="POST" autocomplete="off">
                 <div class="form-grid">
                    <div class="form-group"><label for="name2">Full Name</label><input type="text" name="name2" required></div>
                    <div class="form-group"><label for="staffid">Staff ID</label><input type="text" name="staffid" required></div>
                    <div class="form-group"><label for="mail2">Email Address</label><input type="email" name="mail2" required></div>
                    <div class="form-group"><label for="phno2">Phone Number</label><input type="tel" name="phno2" pattern="[6789][0-9]{9}" required></div>
                    <div class="form-group"><label for="dept2">Department</label>
                        <select name="dept2" required><option value="CSE">CSE</option><option value="ISE">ISE</option><option value="ECE">ECE</option><option value="EEE">EEE</option></select>
                    </div>
                     <div class="form-group"><label for="dob2">Date of Joining</label><input type="date" name="dob2" required></div>
                </div>
                <div class="form-group"><label>Gender</label>
                    <div style="display:flex; gap:1.5rem;"><label><input type="radio" name="gender2" value="M" checked> Male</label><label><input type="radio" name="gender2" value="F"> Female</label></div>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label for="password2">Password</label><input type="password" name="password2" required></div>
                    <div class="form-group"><label for="cpassword2">Confirm Password</label><input type="password" name="cpassword2" required></div>
                </div>
                <button type="submit" name="staffsu" class="btn btn-solid" style="width:100%;">Create Staff Account</button>
            </form>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        const links = document.querySelectorAll('.tab-link');
        links.forEach(link => link.classList.remove('active'));
        document.querySelector(`.tab-link[onclick="showTab('${tabName}')"]`).classList.add('active');
    }
</script>

<?php
include_once 'footer.php';
?>