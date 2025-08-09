<?php
// Your existing PHP logic to fetch student data is perfect.
// Make sure header.php is included before this block.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'sql.php';
$conn = mysqli_connect($host, $user, $ps, $project);
if (!$conn) {
    echo "<script>alert(\"Database error retry after some time !\")</script>";
} else {
    // Check if the user is logged in and the USN is set in the session
    if (!isset($_SESSION["usn"])) {
        // If not logged in, redirect them to the login page
        header("Location: login.php");
        exit();
    }
    $usn = $_SESSION["usn"];
    $sql = "select * from student where usn='{$usn}'";
    $res =   mysqli_query($conn, $sql);
    if ($res == true) {
        $row = mysqli_fetch_array($res);
        $dbusn = $row['usn'];
        $dbname = $row['name'];
        $dbmail = $row['mail'];
        $dbphno = $row['phno'];
        $dbgender = $row['gender'];
        $dbdob = $row['DOB'];
        $dbdept = $row['dept'];
    }
}

// Include header AFTER all PHP logic
include_once 'header.php';
?>

<style>
    /* Additional styles for the profile page */
    .profile-container {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 2rem;
        align-items: flex-start;
    }
    .profile-avatar-card {
        text-align: center;
    }
    .profile-avatar-card img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--border-color);
        margin-bottom: 1rem;
    }
    .profile-details-card .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    .profile-details-card .detail-item:last-child {
        border-bottom: none;
    }
    .detail-item .label {
        font-weight: 500;
        color: var(--text-secondary);
    }
    .detail-item .value {
        font-weight: 500;
        color: var(--text-primary);
    }
    @media (max-width: 768px) {
        .profile-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container">
    <h2 style="margin-bottom: 2rem;">Student Profile</h2>

    <div class="profile-container">
        <div class="card profile-avatar-card">
            <img src="assets/img/student.jpg" alt="Student Avatar">
            <h3><?php echo htmlspecialchars($dbname); ?></h3>
            <p style="color: var(--text-secondary);"><?php echo htmlspecialchars($dbdept); ?> Engineering</p>
        </div>

        <div class="card profile-details-card">
            <div class="detail-item">
                <span class="label">Full Name</span>
                <span class="value"><?php echo htmlspecialchars($dbname); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">USN</span>
                <span class="value"><?php echo htmlspecialchars($dbusn); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Email</span>
                <span class="value"><?php echo htmlspecialchars($dbmail); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Phone</span>
                <span class="value"><?php echo htmlspecialchars($dbphno); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Department</span>
                <span class="value"><?php echo htmlspecialchars($dbdept); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Gender</span>
                <span class="value"><?php echo htmlspecialchars($dbgender); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Date of Birth</span>
                <span class="value"><?php echo htmlspecialchars($dbdob); ?></span>
            </div>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>