<?php
// Start session and check if the user is logged in as a student
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usn'])) {
    header("Location: login.php");
    exit();
}

// Include the modern PDO database connection
require_once 'sql.php'; // This creates the $pdo object
include_once 'header.php';

$scores = [];
$db_error = null;

try {
    $usn = $_SESSION['usn'];
    // This query gets the data we need using a prepared statement
    $sql = "SELECT q.quizname, sc.score, sc.totalscore 
            FROM score sc 
            JOIN quiz q ON sc.quizid = q.quizid 
            WHERE sc.usn = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usn]);
    $scores = $stmt->fetchAll();

} catch (PDOException $e) {
    $db_error = "A database error occurred. Please try again later.";
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
                    <?php if ($db_error): ?>
                        <tr><td colspan="4" style="text-align: center; color:#f87171;"><?php echo $db_error; ?></td></tr>
                    <?php elseif (count($scores) > 0): ?>
                        <?php foreach ($scores as $row): ?>
                            <?php
                                $quizname = htmlspecialchars($row["quizname"]);
                                $score = (int) $row["score"];
                                $totalscore = (int) $row["totalscore"];

                                // Calculate the remark here to ensure it's always correct.
                                if ($totalscore > 0) {
                                    $remark = (($score / $totalscore) >= 0.5) ? 'Pass' : 'Fail';
                                } else {
                                    $remark = 'Fail';
                                }
                                
                                $remark_class = ($remark === 'Pass') ? 'remark-pass' : 'remark-fail';
                            ?>
                            <tr>
                                <td><?php echo $quizname; ?></td>
                                <td style='text-align: center;'><?php echo $score; ?></td>
                                <td style='text-align: center;'><?php echo $totalscore; ?></td>
                                <td style='text-align: center;'><span class='remark <?php echo $remark_class; ?>'><?php echo $remark; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan='4' style='text-align: center; color: var(--text-secondary);'>You haven't taken any quizzes yet. <a href='homestud.php'>Click here</a> to get started!</td></tr>
                    <?php endif; ?>
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