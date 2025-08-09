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

$feedback = null;

// --- Handle Delete Request ---
if (isset($_POST['delete_quiz']) && isset($_POST['quizid_to_delete'])) {
    try {
        $quizid_to_delete = $_POST['quizid_to_delete'];
        
        // For a complete application, you should also delete related questions and scores.
        // This is called a cascading delete. For now, we delete the quiz itself.
        $sql = "DELETE FROM quiz WHERE quizid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quizid_to_delete]);
        
        $feedback = ['message' => 'Quiz deleted successfully!', 'type' => 'success'];

    } catch (PDOException $e) {
        // This error often happens if there are still questions or scores linked to the quiz
        $feedback = ['message' => 'Error: Cannot delete this quiz. Please make sure all associated questions and scores are removed first.', 'type' => 'error'];
    }
}

include_once 'header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2><i class="fa fa-list-alt"></i> Manage Quizzes</h2>
        <a href="addq.php" class="btn btn-solid"><i class="fa fa-plus"></i> Create New Quiz</a>
    </div>
    
    <?php if ($feedback): ?>
        <div class="message <?php echo $feedback['type']; ?>">
            <?php echo htmlspecialchars($feedback['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <p>A list of all quizzes currently in the system.</p>
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
                        $sql = "SELECT * FROM quiz ORDER BY quizid DESC";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute();
                        $quizzes = $stmt->fetchAll();
                    } catch (PDOException $e) {
                        $db_error = "A database error occurred.";
                    }

                    if ($db_error): ?>
                        <tr><td colspan='3' style='text-align:center; color:#f87171;'><?php echo $db_error; ?></td></tr>
                    <?php elseif (count($quizzes) > 0): ?>
                        <?php foreach ($quizzes as $row):
                            $quiz_id = htmlspecialchars($row['quizid']);
                            $quiz_name = htmlspecialchars($row['quizname']);
                        ?>
                            <tr>
                                <td><?php echo $quiz_id; ?></td>
                                <td><?php echo $quiz_name; ?></td>
                                <td style='text-align: right;'>
                                    <a href='addqs.php?q=<?php echo $quiz_id; ?>' class='btn' style='margin-right: 0.5rem;'>Add/View Q's</a>
                                    <form method='POST' action='quizlist.php' style='display:inline;' onsubmit='return confirm("Are you sure you want to delete this quiz? This cannot be undone.");'>
                                        <input type='hidden' name='quizid_to_delete' value='<?php echo $quiz_id; ?>'>
                                        <button type='submit' name='delete_quiz' class='btn' style='background-color:#991b1b; border-color:#991b1b; color:#fee2e2;'>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan='3' style='text-align:center; color:var(--text-secondary);'>No quizzes have been created yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.success { background-color: #166534; color: #dcfce7; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>
<?php
include_once 'footer.php';
?>