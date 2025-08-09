<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['staffid'])) { header("Location: login.php"); exit(); }
require_once 'sql.php';
include_once 'header.php';
?>

<div class="container">
    <h2 style="margin-bottom: 0.5rem;">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
    <p style="color:var(--text-secondary); margin-top:0; margin-bottom:2rem;">This is your staff dashboard. Manage quizzes and monitor students from here.</p>

    <div class="dashboard-grid">
        <a href="addq.php" class="card-link">
            <div class="action-card">
                <i class="fa fa-plus-circle"></i>
                <h3>Add New Quiz</h3>
                <p>Create a new quiz and add questions.</p>
            </div>
        </a>
        <a href="quizlist.php" class="card-link">
            <div class="action-card">
                <i class="fa fa-list-alt"></i>
                <h3>View All Quizzes</h3>
                <p>Edit or manage existing quizzes.</p>
            </div>
        </a>
        <a href="staffleaderboard.php" class="card-link">
             <div class="action-card">
                <i class="fa fa-trophy"></i>
                <h3>View Leaderboard</h3>
                <p>See student rankings and scores.</p>
            </div>
        </a>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3><i class="fa fa-history"></i> Recently Added Quizzes</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Quiz Title</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $quizzes = [];
                    $db_error = null;
                    try {
                        $sql = "SELECT * FROM quiz ORDER BY quizid DESC LIMIT 5";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $quizzes = $stmt->fetchAll();
                    } catch (PDOException $e) {
                        $db_error = "A database error occurred.";
                    }
                    
                    if ($db_error): ?>
                        <tr><td colspan='3' style='text-align:center;'><?php echo $db_error; ?></td></tr>
                    <?php elseif (count($quizzes) > 0): ?>
                        <?php foreach ($quizzes as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['quizid']); ?></td>
                                <td><?php echo htmlspecialchars($row['quizname']); ?></td>
                                <td style='text-align: right;'>
                                    <a href='addqs.php?q=<?php echo htmlspecialchars($row['quizid']); ?>' class='btn btn-secondary'>Add/View Q's</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan='3' style='text-align:center;'>No quizzes created yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
    .action-card { background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; height: 100%; }
    .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.25); border-color: var(--primary-color); }
    .action-card i { font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem; }
    .action-card h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--text-primary); }
    .action-card p { color: var(--text-secondary); font-size: 0.9rem; }
    a.card-link { text-decoration: none; }
</style>

<?php include_once 'footer.php'; ?>