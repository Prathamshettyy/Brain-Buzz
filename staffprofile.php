<?php
// Start session and check if the user is logged in as a staff member
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['staffid'])) {
    header("Location: login.php");
    exit();
}

// Include the modern PDO database connection
require_once 'sql.php'; // This creates the $pdo object
include_once 'header.php';

$staff_details = null;
$db_error = null;

try {
    $staffid = $_SESSION["staffid"];
    // Use a prepared statement to securely fetch the staff member's data
    $sql = "SELECT * FROM staff WHERE staffid = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$staffid]);
    $staff_details = $stmt->fetch();

    if (!$staff_details) {
        $db_error = "Could not retrieve your profile information.";
    }

} catch (PDOException $e) {
    $db_error = "A database error occurred. Please try again later.";
}
?>

<div class="container">
    <h2 style="margin-bottom: 2rem;">Staff Profile</h2>

    <?php if ($db_error): ?>
        <div class="card" style="text-align:center; color:#f87171;">
            <p><?php echo $db_error; ?></p>
        </div>
    <?php elseif ($staff_details): ?>
        <div class="profile-container">
            <div class="card profile-avatar-card">
                <img src="assets/img/teacher.jpg" alt="Staff Avatar">
                <h3><?php echo htmlspecialchars($staff_details['name'] ?? 'Staff Member'); ?></h3>
                <p style="color: var(--text-secondary);"><?php echo htmlspecialchars($staff_details['dept'] ?? 'N/A'); ?> Department</p>
            </div>

            <div class="card profile-details-card">
                <div class="detail-item">
                    <span class="label">Full Name</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['name'] ?? 'Not provided'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Staff ID</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['staffid'] ?? 'Not provided'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Email</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['mail'] ?? 'Not provided'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Phone</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['phno'] ?? 'Not provided'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Department</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['dept'] ?? 'Not provided'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Gender</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['gender'] ?? 'Not provided'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="label">Date of Joining</span>
                    <span class="value"><?php echo htmlspecialchars($staff_details['DOB'] ?? 'Not provided'); ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .profile-container { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: flex-start; }
    .profile-avatar-card { text-align: center; }
    .profile-avatar-card img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--border-color); margin-bottom: 1rem; }
    .profile-details-card .detail-item { display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color); }
    .profile-details-card .detail-item:last-child { border-bottom: none; }
    .detail-item .label { font-weight: 500; color: var(--text-secondary); }
    .detail-item .value { font-weight: 500; color: var(--text-primary); }
    @media (max-width: 768px) { .profile-container { grid-template-columns: 1fr; } }
</style>

<?php
include_once 'footer.php';
?>