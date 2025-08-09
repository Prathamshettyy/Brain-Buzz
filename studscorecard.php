<?php
// Start session and check if the user is logged in as a student
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
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
    <div class="card">
        <div class="card-header">
            <h2><i class="fa fa-poll"></i> Your Scorecard</h2>
            <p>Here are the results of the quizzes you have completed.</p>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quiz Title</th>
                        <th style="text-align: center;">Score</th>
                        <th style="text-align: center;">Total Questions</th>
                        <th style="text-align: center;">Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($conn)) {
                        $usn = $_SESSION['usn'];
                        // This query gets the data we need
                        $sql = "SELECT q.quizname, sc.score, sc.totalscore 
                                FROM score sc 
                                JOIN quiz q ON sc.quizid = q.quizid 
                                WHERE sc.usn = '{$usn}'";
                        
                        $res = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $quizname = htmlspecialchars($row["quizname"]);
                                $score = (int) $row["score"]; // Cast to integer
                                $totalscore = (int) $row["totalscore"]; // Cast to integer

                                // **FIX:** Calculate the remark here to ensure it's always correct.
                                // A score of 50% or higher is a "Pass".
                                if ($totalscore > 0) {
                                    $remark = (($score / $totalscore) >= 0.5) ? 'Pass' : 'Fail';
                                } else {
                                    $remark = 'Fail';
                                }
                                
                                $remark_class = ($remark === 'Pass') ? 'remark-pass' : 'remark-fail';

                                echo "<tr>
                                        <td>{$quizname}</td>
                                        <td style='text-align: center;'>{$score}</td>
                                        <td style='text-align: center;'>{$totalscore}</td>
                                        <td style='text-align: center;'><span class='remark {$remark_class}'>{$remark}</span></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align: center; color: var(--text-secondary);'>You haven't taken any quizzes yet. <a href='homestud.php'>Click here</a> to get started!</td></tr>";
                        }
                        mysqli_close($conn);
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center; color:#f87171;'>{$db_error}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .remark {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .remark.remark-pass {
        background-color: rgba(74, 222, 128, 0.2); /* Green */
        color: #4ade80;
    }
    .remark.remark-fail {
        background-color: rgba(248, 113, 113, 0.2); /* Red */
        color: #f87171;
    }
</style>

<?php
include_once 'footer.php';
?>