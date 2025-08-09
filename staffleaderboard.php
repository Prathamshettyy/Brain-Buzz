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

$leaderboard_data = [];
$db_error = null;

try {
    // This query correctly gets the total score for students who have taken a quiz
    $sql = "SELECT s.name, s.usn, SUM(sc.score) AS totalscore 
            FROM student s 
            INNER JOIN score sc ON s.usn = sc.usn 
            GROUP BY s.usn, s.name
            ORDER BY totalscore DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $leaderboard_data = $stmt->fetchAll();

} catch (PDOException $e) {
    $db_error = "A database error occurred. Please try again later.";
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa fa-trophy"></i> Student Leaderboard</h2>
            <p>Ranking of students who have completed at least one quiz.</p>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Rank</th>
                        <th>Student Name</th>
                        <th>USN</th>
                        <th style="text-align: right;">Total Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($db_error): ?>
                        <tr><td colspan="4" style="text-align: center; color:#f87171;"><?php echo $db_error; ?></td></tr>
                    <?php elseif (count($leaderboard_data) > 0): ?>
                        <?php 
                        $rank = 1;
                        foreach ($leaderboard_data as $row): 
                            $student_name = htmlspecialchars($row['name']);
                            $student_usn = htmlspecialchars($row['usn']);
                            $total_score = htmlspecialchars($row['totalscore']);
                        ?>
                            <tr>
                                <td><strong>#<?php echo $rank; ?></strong></td>
                                <td><?php echo $student_name; ?></td>
                                <td><?php echo $student_usn; ?></td>
                                <td style='text-align: right;'><strong><?php echo $total_score; ?></strong></td>
                            </tr>
                        <?php 
                            $rank++;
                        endforeach; 
                        ?>
                    <?php else: ?>
                        <tr><td colspan="4" style='text-align:center; color:var(--text-secondary);'>No students have completed a quiz yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>