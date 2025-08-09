<?php
// Start session and check if the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in as a student
if (!isset($_SESSION['usn'])) {
    header("Location: login.php");
    exit();
}

// Include database connection and establish a connection
require_once 'sql.php';
$conn = mysqli_connect($host, $user, $ps, $project);
if (!$conn) {
    $db_error = "Could not connect to the database. Please try again later.";
}

// --- Logic to Handle Quiz Submission ---
if (isset($_POST['submit_quiz']) && isset($_POST['quizid'])) {
    if (isset($conn)) {
        $quizid = mysqli_real_escape_string($conn, $_POST['quizid']);
        $usn = $_SESSION['usn']; 
        $submitted_answers = $_POST['answers']; // This will be a numerically indexed array
        
        $score = 0;
        
        // Fetch the correct answers from the database IN ORDER
        $sql_answers = "SELECT answer FROM questions WHERE quizid = '{$quizid}'";
        $res_answers = mysqli_query($conn, $sql_answers);
        
        $correct_answers = [];
        while($row = mysqli_fetch_assoc($res_answers)) {
            $correct_answers[] = $row['answer'];
        }

        // Compare submitted answers with correct answers by index
        foreach ($submitted_answers as $index => $user_answer) {
            if (isset($correct_answers[$index]) && $user_answer === $correct_answers[$index]) {
                $score++;
            }
        }
        
        $total_questions = count($correct_answers);
        $remark = ($score / $total_questions) >= 0.5 ? 'Pass' : 'Fail';

        $insert_sql = "INSERT INTO score (score, usn, quizid, totalscore, remark) VALUES ('$score', '$usn', '$quizid', '$total_questions', '$remark')";
        
        if(mysqli_query($conn, $insert_sql)) {
            header("Location: studscorecard.php");
            exit();
        } else {
             $db_error = "Error saving your score. You may have already completed this quiz.";
        }
    }
}

// Include the header AFTER all the submission logic
include_once 'header.php';
?>

<style>
    .question-card { background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .question-text { font-size: 1.2rem; font-weight: 500; margin-bottom: 1.5rem; color: var(--text-primary); }
    .options-group label { display: block; background-color: var(--bg-color); padding: 1rem; margin-bottom: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); cursor: pointer; transition: border-color 0.3s ease, background-color 0.3s ease; }
    .options-group label:hover { border-color: var(--primary-color); }
    .options-group input[type="radio"] { margin-right: 10px; }
</style>

<div class="container">
    <?php
    // --- Logic to Display The Quiz ---
    if (isset($_GET['q']) && isset($conn)) {
        $quizid = mysqli_real_escape_string($conn, $_GET['q']);

        $sql_quiz_title = "SELECT quizname FROM quiz WHERE quizid = '{$quizid}'";
        $res_quiz_title = mysqli_query($conn, $sql_quiz_title);
        $quiz_row = mysqli_fetch_assoc($res_quiz_title);
        $quiz_name = $quiz_row ? htmlspecialchars($quiz_row['quizname']) : "Quiz";

        $sql_questions = "SELECT * FROM questions WHERE quizid = '{$quizid}'";
        $res_questions = mysqli_query($conn, $sql_questions);
        
        echo "<h2 style='margin-bottom:1rem;'>Taking Quiz: {$quiz_name}</h2>";

        if (mysqli_num_rows($res_questions) > 0) {
            echo "<form method='POST' action='takeq.php'>";
            echo "<input type='hidden' name='quizid' value='{$quizid}'>";
            
            $q_index = 0; // Use a simple numeric index
            while ($row = mysqli_fetch_assoc($res_questions)) {
                $question = htmlspecialchars($row['qs']);
                $options = [
                    htmlspecialchars($row['op1']),
                    htmlspecialchars($row['op2']),
                    htmlspecialchars($row['op3']),
                    htmlspecialchars($row['answer'])
                ];
                shuffle($options);

                echo "<div class='question-card'>";
                echo "<p class='question-text'>".($q_index + 1).". {$question}</p>";
                echo "<div class='options-group'>";
                
                foreach ($options as $option) {
                    // **FIX:** The name of the radio button now uses a simple numeric index.
                    echo "<label><input type='radio' name='answers[{$q_index}]' value='{$option}' required> {$option}</label>";
                }

                echo "</div></div>";
                $q_index++;
            }

            echo "<button type='submit' name='submit_quiz' class='btn btn-solid' style='width:100%; padding: 1rem;'>Submit My Answers</button>";
            echo "</form>";

        } else {
            echo "<div class='card' style='text-align:center;'><p>This quiz has no questions yet. Please check back later.</p></div>";
        }
    } elseif (isset($db_error)) {
        echo "<div class='card' style='text-align:center;'><p style='color:#f87171;'>{$db_error}</p></div>";
    } else {
         echo "<div class='card' style='text-align:center;'><p>No quiz selected. Please go back to the dashboard and choose a quiz.</p></div>";
    }

    if (isset($conn)) { mysqli_close($conn); }
    ?>
</div>

<?php
include_once 'footer.php';
?>