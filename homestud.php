<?php
// Start session and check if the user is logged in as a student
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in or not a student
if (!isset($_SESSION['usn'])) {
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
    <h2 style="margin-bottom: 0.5rem;">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p style="color:var(--text-secondary); margin-top:0; margin-bottom:2rem;">Ready to test your knowledge?</p>

    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-list-alt"></i> Available Quizzes</h3>
            <p style="color:var(--text-secondary); margin-top:-0.5rem;">Select a quiz from the list below to begin.</p>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quiz Title</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch and display quizzes from the database
                    if (isset($conn)) {
                        $sql = "SELECT * FROM quiz";
                        $res = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                // Sanitize data before displaying
                                $quiz_id = htmlspecialchars($row['quizid']);
                                $quiz_name = htmlspecialchars($row['quizname']);
                                
                                // **FIX:** Removed the 'subject' column from the display
                                echo "<tr>
                                        <td>{$quiz_name}</td>
                                        <td style='text-align: right;'>
                                            <a href='takeq.php?q={$quiz_id}' class='btn'>Take Quiz</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' style='text-align:center; color:var(--text-secondary);'>No quizzes are available at the moment. Please check back later.</td></tr>";
                        }
                        mysqli_close($conn);
                    } else {
                        // Display database connection error
                        echo "<tr><td colspan='2' style='text-align:center; color:#f87171;'>{$db_error}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
        <div class="card">
            <h3><i class="fa fa-trophy"></i> Your Scorecard</h3>
            <p style="color:var(--text-secondary);">Check your past performance and scores.</p>
            <br>
            <a href="studscorecard.php" class="btn btn-secondary">View My Scores</a>
        </div>
        <div class="card">
            <h3><i class="fa fa-users"></i> Leaderboard</h3>
            <p style="color:var(--text-secondary);">See how you rank among your peers.</p>
            <br>
            <a href="studleaderboard.php" class="btn btn-secondary">View Leaderboard</a>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>