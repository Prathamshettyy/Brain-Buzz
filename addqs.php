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

// Get Quiz ID and Name from URL (this is the modern, reliable way)
$quizid = null;
$quiz_name = 'No Quiz Selected';
if (isset($_GET['q'])) {
    $quizid = mysqli_real_escape_string($conn, $_GET['q']);
    $title_sql = "SELECT quizname FROM quiz WHERE quizid = '{$quizid}'";
    $title_res = mysqli_query($conn, $title_sql);
    if ($title_row = mysqli_fetch_assoc($title_res)) {
        $quiz_name = htmlspecialchars($title_row['quizname']);
    }
} else {
    // If no quiz ID in URL, redirect back to the list
    header("Location: quizlist.php");
    exit();
}


// --- Handle Form Submission for Adding a Question ---
function addQuestion($conn, $quizid) {
    $qs = mysqli_real_escape_string($conn, $_POST["qs"]);
    $op1 = mysqli_real_escape_string($conn, $_POST["op1"]);
    $op2 = mysqli_real_escape_string($conn, $_POST["op2"]);
    $op3 = mysqli_real_escape_string($conn, $_POST["op3"]);
    $ans = mysqli_real_escape_string($conn, $_POST["ans"]);
    
    $sql = "INSERT INTO questions(qs, op1, op2, op3, answer, quizid) VALUES('$qs', '$op1', '$op2', '$op3', '$ans', '$quizid')";
    
    if (mysqli_query($conn, $sql)) {
        return ['message' => 'Question added successfully!', 'type' => 'success'];
    } else {
        return ['message' => 'Error: This question might already exist.', 'type' => 'error'];
    }
}

$form_feedback = null;
// Check if "Add & Stay" button was clicked
if (isset($_POST['add_another'])) {
    if (isset($conn) && $quizid) {
        $form_feedback = addQuestion($conn, $quizid);
    }
}

// Check if "Add & Finish" button was clicked
if (isset($_POST['finish'])) {
    if (isset($conn) && $quizid) {
        $form_feedback = addQuestion($conn, $quizid);
        // If question was added successfully, then redirect
        if ($form_feedback['type'] === 'success') {
            header("Location: quizlist.php");
            exit();
        }
    }
}


// Include the header AFTER all PHP logic
include_once 'header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2><i class="fa fa-plus-circle"></i> Add Questions to: <?php echo $quiz_name; ?></h2>
        <a href="quizlist.php" class="btn"><i class="fa fa-arrow-left"></i> Back to Quiz List</a>
    </div>

    <div class="manage-questions-layout">
        <div class="card">
            <div class="card-header">
                <h3>New Question Details</h3>
            </div>
            
            <?php if (!empty($form_feedback)): ?>
                <div class="message <?php echo $form_feedback['type']; ?>">
                    <?php echo $form_feedback['message']; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="addqs.php?q=<?php echo $quizid; ?>" autocomplete="off">
                <div class="form-group">
                    <label for="qs">Question Text</label>
                    <textarea id="qs" name="qs" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="op1">Option 1 (Wrong Answer)</label>
                    <input type="text" id="op1" name="op1" required>
                </div>
                <div class="form-group">
                    <label for="op2">Option 2 (Wrong Answer)</label>
                    <input type="text" id="op2" name="op2" required>
                </div>
                <div class="form-group">
                    <label for="op3">Option 3 (Wrong Answer)</label>
                    <input type="text" id="op3" name="op3" required>
                </div>
                 <div class="form-group">
                    <label for="ans">Correct Answer</label>
                    <input type="text" id="ans" name="ans" required style="border-color: #4ade80;">
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="add_another" class="btn btn-secondary" style="flex-grow: 1;">Add & Stay on Page</button>
                    <button type="submit" name="finish" class="btn btn-solid" style="flex-grow: 1;">Add & Finish</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                 <h3>Existing Questions</h3>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($conn) && $quizid) {
                            $list_sql = "SELECT qs FROM questions WHERE quizid = '{$quizid}'";
                            $list_res = mysqli_query($conn, $list_sql);
                            if ($list_res && mysqli_num_rows($list_res) > 0) {
                                $q_num = 1;
                                while ($row = mysqli_fetch_assoc($list_res)) {
                                    $q_text = htmlspecialchars($row['qs']);
                                    echo "<tr>
                                            <td>{$q_num}</td>
                                            <td>{$q_text}</td>
                                          </tr>";
                                    $q_num++;
                                }
                            } else {
                                echo "<tr><td colspan='2' style='text-align:center; color:var(--text-secondary);'>No questions have been added yet.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .manage-questions-layout { display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; align-items: flex-start; }
    @media (max-width: 992px) { .manage-questions-layout { grid-template-columns: 1fr; } }
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.success { background-color: #166534; color: #dcfce7; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>

<?php
if (isset($conn)) { mysqli_close($conn); }
include_once 'footer.php';
?>