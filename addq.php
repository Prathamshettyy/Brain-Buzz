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

$form_error = null;

// --- Handle Form Submission for Creating a New Quiz ---
if (isset($_POST['create_quiz'])) {
    try {
        $quiz_name = $_POST['quiz_name'];
        $staff_id = $_SESSION['staffid']; 
        
        // Use a prepared statement to securely insert the new quiz
        $sql = "INSERT INTO quiz (quizname, staffid) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$quiz_name, $staff_id])) {
            // Get the ID of the quiz we just created
            $new_quiz_id = $pdo->lastInsertId();
            
            // Redirect to the add questions page for the new quiz
            header("Location: addqs.php?q=" . $new_quiz_id);
            exit();
        } else {
            $form_error = "Failed to create the quiz.";
        }
    } catch (PDOException $e) {
        // This error often happens if the quiz name is not unique
        if ($e->getCode() == 23000 || $e->getCode() == 23505) {
            $form_error = "Error: A quiz with this name already exists.";
        } else {
            $form_error = "A database error occurred.";
        }
    }
}

// Include the header AFTER all PHP logic
include_once 'header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2><i class="fa fa-plus-circle"></i> Create a New Quiz</h2>
        <a href="quizlist.php" class="btn"><i class="fa fa-arrow-left"></i> Back to Quiz List</a>
    </div>

    <div class="form-container" style="max-width: 600px;">
        <div class="card">
            <div class="card-header">
                <h3>Quiz Details</h3>
                <p>Enter a name for your new quiz to get started.</p>
            </div>
            
            <?php if ($form_error): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($form_error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="addq.php" autocomplete="off">
                <div class="form-group">
                    <label for="quiz_name">Quiz Name</label>
                    <input type="text" id="quiz_name" name="quiz_name" placeholder="e.g., 'General Knowledge' or 'PHP Basics'" required>
                </div>
                <button type="submit" name="create_quiz" class="btn btn-solid" style="width:100%;">Create Quiz and Add Questions</button>
            </form>
        </div>
    </div>
</div>

<style>
    /* These styles are for the success/error messages */
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>

<?php
include_once 'footer.php';
?>