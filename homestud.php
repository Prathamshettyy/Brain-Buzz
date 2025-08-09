<?php
// Start session and include the new PDO connection file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usn'])) {
    header("Location: login.php");
    exit();
}

require_once 'sql.php'; // This now creates the $pdo object
include_once 'header.php';

$db_error = null;
$quizzes = []; // Create an empty array to hold the quiz data

try {
    // This query correctly fetches ALL quizzes from the database
    $sql = "SELECT * FROM quiz ORDER BY quizid DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $quizzes = $stmt->fetchAll(); // Gets all results
} catch (PDOException $e) {
    $db_error = "A database error occurred. Please try again later.";
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
                    <?php if ($db_error): ?>
                        <tr><td colspan='2' style='text-align:center; color:#f87171;'><?php echo $db_error; ?></td></tr>
                    <?php elseif (count($quizzes) > 0): ?>
                        <?php foreach ($quizzes as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['quizname']); ?></td>
                                <td style='text-align: right;'>
                                    <a href='takeq.php?q=<?php echo htmlspecialchars($row['quizid']); ?>' class='btn'>Take Quiz</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan='2' style='text-align:center; color:var(--text-secondary);'>No quizzes are available at the moment.</td></tr>
                    <?php endif; ?>
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