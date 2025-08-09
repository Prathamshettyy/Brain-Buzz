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

$staff_id = $_SESSION['staffid'];
$feedback = null;

// --- Handle Add Quiz Request using PDO ---
if (isset($_POST['add_quiz'])) {
    try {
        $quiz_name = $_POST['quiz_name'];
        $sql = "INSERT INTO quiz (quizname, staffid) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quiz_name, $staff_id]);
        
        $new_quiz_id = $pdo->lastInsertId();
        header("Location: addqs.php?q=" . $new_quiz_id);
        exit();
    } catch (PDOException $e) {
        $feedback = ['message' => 'Error: A quiz with this name may already exist.', 'type' => 'error'];
    }
}

// --- Handle Delete Quiz Request using PDO ---
if (isset($_POST['delete_quiz'])) {
    try {
        $quizid_to_delete = $_POST['quizid_to_delete'];
        $sql = "DELETE FROM quiz WHERE quizid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quizid_to_delete]);
        $feedback = ['message' => 'Quiz deleted successfully!', 'type' => 'success'];
    } catch (PDOException $e) {
        $feedback = ['message' => 'Error: Cannot delete this quiz. Please remove all associated questions and scores first.', 'type' => 'error'];
    }
}

include_once 'header.php';
?>

<div class="container">
    <h2 style="margin-bottom: 1.5rem;">Staff Dashboard</h2>

    <div class="card">
        <nav class="tab-nav">
            <a class="tab-link active" onclick="showTab('add_quiz_tab')"><i class="fa fa-plus-circle"></i> Add Quiz</a>
            <a class="tab-link" onclick="showTab('manage_quiz_tab')"><i class="fa fa-list-alt"></i> Manage Quizzes</a>
        </nav>
        
        <?php if ($feedback): ?>
            <div class="message <?php echo $feedback['type']; ?>">
                <?php echo htmlspecialchars($feedback['message']); ?>
            </div>
        <?php endif; ?>

        <div id="add_quiz_tab" class="tab-content active" style="padding: 1.5rem;">
            <form method="POST" action="homestaff.php" autocomplete="off">
                <div class="form-group">
                    <label for="quiz_name">New Quiz Name</label>
                    <input type="text" id="quiz_name" name="quiz_name" placeholder="e.g., 'Advanced PHP Concepts'" required>
                </div>
                <button type="submit" name="add_quiz" class="btn btn-solid" style="width:100%;">Create & Add Questions</button>
            </form>
        </div>

        <div id="manage_quiz_tab" class="tab-content" style="padding: 1.5rem;">
            <p style="color:var(--text-secondary); margin-top:-1rem; margin-bottom:1.5rem;">View, manage questions for, or delete existing quizzes.</p>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
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
                            <tr><td colspan='2' style='text-align:center;'><?php echo $db_error; ?></td></tr>
                        <?php elseif (count($quizzes) > 0): ?>
                            <?php foreach ($quizzes as $row):
                                $quiz_id = htmlspecialchars($row['quizid']);
                                $quiz_name = htmlspecialchars($row['quizname']);
                            ?>
                                <tr>
                                    <td><?php echo $quiz_name; ?></td>
                                    <td style='text-align: right;'>
                                        <a href='addqs.php?q=<?php echo $quiz_id; ?>' class='btn' style='margin-right: 0.5rem;'>Add/View Q's</a>
                                        <form method='POST' action='homestaff.php' style='display:inline;' onsubmit='return confirm("Are you sure you want to delete this quiz?");'>
                                            <input type='hidden' name='quizid_to_delete' value='<?php echo $quiz_id; ?>'>
                                            <button type='submit' name='delete_quiz' class='btn' style='background-color:#991b1b; border-color:#991b1b; color:#fee2e2;'>Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan='2' style='text-align:center;'>No quizzes created yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .tab-nav { display: flex; border-bottom: 1px solid var(--border-color); }
    .tab-link { padding: 1rem 1.5rem; cursor: pointer; font-weight: 500; color: var(--text-secondary); border-bottom: 3px solid transparent; }
    .tab-link.active { color: var(--primary-color); border-bottom-color: var(--primary-color); }
    .tab-link i { margin-right: 0.5rem; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .message { padding: 1rem; margin: 0 1.5rem 1.5rem 1.5rem; border-radius: 6px; text-align: center; font-weight: 500; }
    .message.success { background-color: #166534; color: #dcfce7; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>

<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
        event.currentTarget.classList.add('active');
    }
</script>

<?php
include_once 'footer.php';
?>