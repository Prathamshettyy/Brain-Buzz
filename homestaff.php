<?php
// Start session and check if the user is logged in as a staff member
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['staffid'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'sql.php';
$conn = mysqli_connect($host, $user, $ps, $project);
if (!$conn) {
    $db_error = "Could not connect to the database.";
}

$staff_id = $_SESSION['staffid'];
$form_feedback = null;

// --- Handle Add Quiz Request ---
if (isset($_POST['add_quiz'])) {
    if (isset($conn)) {
        $quiz_name = mysqli_real_escape_string($conn, $_POST['quiz_name']);
        $sql = "INSERT INTO quiz (quizname, staffid) VALUES ('$quiz_name', '$staff_id')";
        if (mysqli_query($conn, $sql)) {
            $new_quiz_id = mysqli_insert_id($conn);
            // Redirect to add questions page
            header("Location: addqs.php?q=" . $new_quiz_id);
            exit();
        } else {
            $form_feedback = ['message' => 'Error: A quiz with this name may already exist.', 'type' => 'error'];
        }
    }
}

// --- Handle Delete Quiz Request ---
if (isset($_POST['delete_quiz'])) {
    if (isset($conn)) {
        $quizid_to_delete = mysqli_real_escape_string($conn, $_POST['quizid_to_delete']);
        $sql = "DELETE FROM quiz WHERE quizid = '{$quizid_to_delete}'";
        if (mysqli_query($conn, $sql)) {
            $form_feedback = ['message' => 'Quiz deleted successfully!', 'type' => 'success'];
        } else {
            $form_feedback = ['message' => 'Error deleting quiz. It may have associated questions or scores.', 'type' => 'error'];
        }
    }
}

// Include the header AFTER all PHP logic
include_once 'header.php';
?>

<div class="container">
    <h2 style="margin-bottom: 1.5rem;">Staff Dashboard</h2>

    <div class="card">
        <nav class="tab-nav">
            <a class="tab-link active" onclick="showTab('add_quiz_tab')"><i class="fa fa-plus-circle"></i> Add Quiz</a>
            <a class="tab-link" onclick="showTab('manage_quiz_tab')"><i class="fa fa-list-alt"></i> Manage Quizzes</a>
        </nav>
        
        <?php if (!empty($form_feedback)): ?>
            <div class="message <?php echo $form_feedback['type']; ?>">
                <?php echo $form_feedback['message']; ?>
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
                        if (isset($conn)) {
                            $sql = "SELECT * FROM quiz ORDER BY quizid DESC";
                            $res = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($res) > 0) {
                                while ($row = mysqli_fetch_assoc($res)) {
                                    $quiz_id = htmlspecialchars($row['quizid']);
                                    $quiz_name = htmlspecialchars($row['quizname']);
                                    echo "<tr>
                                            <td>{$quiz_name}</td>
                                            <td style='text-align: right;'>
                                                <a href='addqs.php?q={$quiz_id}' class='btn' style='margin-right: 0.5rem;'>Add/View Q's</a>
                                                <form method='POST' action='homestaff.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this quiz?\");'>
                                                    <input type='hidden' name='quizid_to_delete' value='{$quiz_id}'>
                                                    <button type='submit' name='delete_quiz' class='btn' style='background-color:#991b1b; border-color:#991b1b; color:#fee2e2;'>Delete</button>
                                                </form>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2' style='text-align:center;'>No quizzes created yet.</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' style='text-align:center;'>{$db_error}</td></tr>";
                        }
                        ?>
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
if (isset($conn)) { mysqli_close($conn); }
include_once 'footer.php';
?>