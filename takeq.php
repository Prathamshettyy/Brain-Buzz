<?php
// Start session and check if the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usn'])) {
    header("Location: login.php");
    exit();
}

// Include the modern PDO database connection
require_once 'sql.php'; // This creates the $pdo object

$feedback = null;

// --- Logic to Handle Quiz Submission ---
if (isset($_POST['submit_quiz']) && isset($_POST['quizid'])) {
    try {
        $quizid = $_POST['quizid'];
        $usn = $_SESSION['usn'];
        $submitted_answers = $_POST['answers'] ?? [];
        $score = 0;

        // Fetch the correct answers from the database using PDO
        $sql_answers = "SELECT answer FROM questions WHERE quizid = ?";
        $stmt_answers = $pdo->prepare($sql_answers);
        $stmt_answers->execute([$quizid]);
        
        // Fetch all correct answers into a simple array
        $correct_answers = $stmt_answers->fetchAll(PDO::FETCH_COLUMN);

        // Compare submitted answers with correct answers by their order
        foreach ($submitted_answers as $index => $user_answer) {
            if (isset($correct_answers[$index]) && $user_answer === $correct_answers[$index]) {
                $score++;
            }
        }
        
        $total_questions = count($correct_answers);
        $remark = ($total_questions > 0 && ($score / $total_questions) >= 0.5) ? 'Pass' : 'Fail';

        // Insert the final score into the database using a prepared statement
        $insert_sql = "INSERT INTO score (score, usn, quizid, totalscore, remark) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($insert_sql);
        $stmt_insert->execute([$score, $usn, $quizid, $total_questions, $remark]);
        
        // Redirect to the scorecard to show the result
        header("Location: studscorecard.php");
        exit();

    } catch (PDOException $e) {
        $feedback = ['message' => 'Error saving your score. You may have already completed this quiz.', 'type' => 'error'];
    }
}

// Include the header AFTER all the submission logic
include_once 'header.php';
?>

<div class="container">
    <?php
    $quiz_name = "Quiz";
    $questions = [];
    $quizid = $_GET['q'] ?? null;

    if ($quizid) {
        try {
            // Fetch quiz title using PDO
            $sql_quiz_title = "SELECT quizname FROM quiz WHERE quizid = ?";
            $stmt_title = $pdo->prepare($sql_quiz_title);
            $stmt_title->execute([$quizid]);
            $quiz_row = $stmt_title->fetch();
            if ($quiz_row) {
                $quiz_name = htmlspecialchars($quiz_row['quizname']);
            }

            // Fetch all questions for the quiz using PDO
            $sql_questions = "SELECT * FROM questions WHERE quizid = ?";
            $stmt_questions = $pdo->prepare($sql_questions);
            $stmt_questions->execute([$quizid]);
            $questions = $stmt_questions->fetchAll();

        } catch (PDOException $e) {
            $feedback = ['message' => 'A database error occurred while loading the quiz.', 'type' => 'error'];
        }
    }
    ?>

    <h2 style='margin-bottom:1rem;'>Taking Quiz: <?php echo $quiz_name; ?></h2>

    <?php if ($feedback): ?>
        <div class="message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
    <?php endif; ?>

    <?php if (count($questions) > 0): ?>
        <form method='POST' action='takeq.php'>
            <input type='hidden' name='quizid' value='<?php echo htmlspecialchars($quizid); ?>'>
            
            <?php
            $q_index = 0;
            foreach ($questions as $row) {
                $question = htmlspecialchars($row['qs']);
                $options = [
                    htmlspecialchars($row['op1']),
                    htmlspecialchars($row['op2']),
                    htmlspecialchars($row['op3']),
                    htmlspecialchars($row['answer'])
                ];
                shuffle($options);
                ?>
                <div class='question-card'>
                    <p class='question-text'><?php echo ($q_index + 1) . ". " . $question; ?></p>
                    <div class='options-group'>
                        <?php foreach ($options as $option): ?>
                            <label><input type='radio' name='answers[<?php echo $q_index; ?>]' value='<?php echo $option; ?>' required> <?php echo $option; ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php
                $q_index++;
            }
            ?>
            <button type='submit' name='submit_quiz' class='btn btn-solid' style='width:100%; padding: 1rem;'>Submit My Answers</button>
        </form>
    <?php elseif (!$feedback && $quizid): ?>
        <div class='card' style='text-align:center;'><p>This quiz has no questions yet. Please check back later.</p></div>
    <?php elseif (!$quizid): ?>
        <div class='card' style='text-align:center;'><p>No quiz selected. Please go back to the dashboard and choose a quiz.</p></div>
    <?php endif; ?>
</div>

<style>
    .question-card { background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .question-text { font-size: 1.2rem; font-weight: 500; margin-bottom: 1.5rem; color: var(--text-primary); }
    .options-group label { display: block; background-color: var(--bg-color); padding: 1rem; margin-bottom: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); cursor: pointer; transition: border-color 0.3s ease, background-color 0.3s ease; }
    .options-group label:hover { border-color: var(--primary-color); }
    .options-group input[type="radio"] { margin-right: 10px; }
    .message { padding: 1rem; border-radius: 6px; text-align: center; margin-bottom: 1.5rem; font-weight: 500; }
    .message.error { background-color: #991b1b; color: #fee2e2; }
</style>

<?php include_once 'footer.php'; ?>