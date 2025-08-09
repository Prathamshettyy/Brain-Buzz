<?php
// Start session and check if the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['usn']) && !isset($_SESSION['staffid'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection and header
require_once 'sql.php';
include_once 'header.php';

// Establish database connection
$conn = mysqli_connect($host, $user, $ps, $project);
if (!$conn) {
    $db_error = "Could not connect to the database. Please try again later.";
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2><i class="fa fa-trophy"></i> Leaderboard</h2>
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
                    <?php
                    if (isset($conn)) {
                        // **FIX:** Reverted to INNER JOIN to show only students with scores.
                        $sql = "SELECT s.name, s.usn, SUM(sc.score) AS totalscore 
                                FROM student s 
                                INNER JOIN score sc ON s.usn = sc.usn 
                                GROUP BY s.usn, s.name
                                ORDER BY totalscore DESC";
                        $res = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($res) > 0) {
                            $rank = 1; 
                            while ($row = mysqli_fetch_assoc($res)) {
                                $student_name = htmlspecialchars($row['name']);
                                $student_usn = htmlspecialchars($row['usn']);
                                $total_score = htmlspecialchars($row['totalscore']);
                                
                                echo "<tr>
                                        <td><strong>#{$rank}</strong></td>
                                        <td>{$student_name}</td>
                                        <td>{$student_usn}</td>
                                        <td style='text-align: right;'><strong>{$total_score}</strong></td>
                                      </tr>";
                                $rank++;
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; color:var(--text-secondary);'>No students have completed a quiz yet.</td></tr>";
                        }
                        mysqli_close($conn);
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; color:#f87171;'>{$db_error}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>