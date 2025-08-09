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

// Get Quiz ID from URL and redirect if it's missing
$quizid = $_GET['q'] ?? null;
if (!$quizid) {
    header("Location: quizlist.php");
    exit();
}

// Function to add a question using PDO
function addQuestion($pdo, $quizid) {
    // Parameter order for the SQL statement
    $params = [
        $_POST["qs"],
        $_POST["op1"],
        $_POST["op2"],
        $_POST["op3"],
        $_POST["ans"],
        $quizid
    ];
    
    try {
        $sql = "INSERT INTO questions(qs, op1, op2, op3, answer, quizid) VALUES(?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return ['message' => 'Question added successfully!', 'type' => 'success'];
    } catch (PDOException $e) {
        // This error often happens if the question text is not unique
        if ($e->getCode() == 23000 || $e->getCode() == 23505) {
            return ['message' => 'Error: This exact question may already exist in the quiz.', 'type' => 'error'];
        }
        return ['message' => 'A database error occurred.', 'type' => 'error'];
    }
}

// Check if "Add & Stay" button was clicked
if (isset($_POST['add_another'])) {
    $feedback = addQuestion($pdo, $quizid);
}

// Check if "Add & Finish" button was clicked
if (isset($_POST['finish'])) {
    $feedback = addQuestion($pdo, $quizid);
    if ($feedback['type'] === 'success') {
        header("Location: quizlist.php");
        exit();
    }
}

include_once 'header.php';
?>

<div class="container">
    <?php
    // Fetch Quiz Name for the header
    $quiz_name = 'Quiz';
    try {
        $title_sql = "SELECT quizname FROM quiz WHERE quizid = ?";
        $title_stmt = $pdo->prepare($title_sql);
        $title_stmt->execute([$quizid]);
        if ($title_row = $title_stmt->fetch()) {
            $quiz_name = htmlspecialchars($title_row['quizname']);
        }
    } catch (PDOException $e) { /* Handle error if needed */ }
    ?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2><i class="fa fa-plus-circle"></i> Add Questions to: <?php echo $quiz_name; ?></h2>
        <a href="quizlist.php" class="btn"><i class="fa fa-arrow-left"></i> Back to Quiz List</a>
    </div>

    <div class="manage-questions-layout">
        <div class="card">
            <div class="card-header"><h3>New Question Details</h3></div>
            
            <?php if ($feedback): ?>
                <div class="message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
            <?php endif; ?>

            <form method="POST" action="addqs.php?q=<?php echo htmlspecialchars($quizid); ?>" autocomplete="off">
                <div class="form-group"><label for="qs">Question Text</label><textarea id="qs" name="qs" rows="3" required></textarea></div>
                <div class="form-group"><label for="op1">Option 1 (Wrong Answer)</label><input type="text" id="op1" name="op1" required></div>
                <div class="form-group"><label for="op2">Option 2 (Wrong Answer)</label><input type="text" id="op2" name="op2" required></div>
                <div class="form-group"><label for="op3">Option 3 (Wrong Answer)</label><input type="text" id="op3" name="op3" required></div>
                <div class="form-group"><label for="ans">Correct Answer</label><input type="text" id="ans" name="ans" required style="border-color: #4ade80;"></div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="add_another" class="btn btn-secondary" style="flex-grow: 1;">Add & Stay on Page</button>
                    <button type="submit" name="finish" class="btn btn-solid" style="flex-grow: 1;">Add & Finish</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header"><h3>Existing Questions</h3></div>
            <div class="table-container">
                <table class="table">
                    <thead><tr><th>#</th><th>Question</th></tr></thead>
                    <tbody>
                        <?php
                        try {
                            $list_sql = "SELECT qs FROM questions WHERE quizid = ?";
                            $list_stmt = $pdo->prepare($list_sql);
                            $list_stmt->execute([$quizid]);
                            $questions = $list_stmt->fetchAll();

                            if (count($questions) > 0) {
                                $q_num = 1;
                                foreach ($questions as $row) {
                                    echo "<tr><td>{$q_num}</td><td>" . htmlspecialchars($row['qs']) . "</td></tr>";
                                    $q_num++;
                                }
                            } else {
                                echo "<tr><td colspan='2' style='text-align:center; color:var(--text-secondary);'>No questions have been added yet.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='2' style='text-align:center; color:#f87171;'>Could not load questions.</td></tr>";
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
    .message.error { background-color: #991b_model
Of course. I have reviewed the `logout.php` file you provided.

The code is **perfectly correct** and already follows best practices for a logout script. It is secure and does exactly what it needs to do: it completely destroys the user's session and redirects them to the homepage.

Because this file has no database connection, it **does not need any changes for PDO**. It will work correctly on both your local server and on Render.

For your final verification, here is the complete and correct code for your `logout.php` file. No changes were needed.

### **`logout.php` (Final and Correct Code)**
```php
<?php
    // Start the session so we can access it
    session_start();

    // Unset all session variables
    session_unset();

    // Destroy the session completely
    session_destroy();

    // Redirect the user to the homepage
    header("location: index.php");
    exit(); // Always call exit() after a header redirect
?>