<?php
// Start session and check if the user is logged in as a staff member
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in or not staff
if (!isset($_SESSION['staffid'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'sql.php';
$conn = mysqli_connect($host, $user, $ps, $project);
if (!$conn) {
    $db_error = "Could not connect to the database. Please try again later.";
}

// --- Handle Delete Request ---
if (isset($_POST['delete_quiz']) && isset($_POST['quizid_to_delete'])) {
    if (isset($conn)) {
        $quizid_to_delete = mysqli_real_escape_string($conn, $_POST['quizid_to_delete']);
        // Note: For a real-world app, you should also delete related questions and scores (cascade delete).
        $delete_sql = "DELETE FROM quiz WHERE quizid = '{$quizid_to_delete}'";
        if (!mysqli_query($conn, $delete_sql)) {
            $delete_error = "Error deleting quiz. It might have associated scores or questions that need to be removed first.";
        }
    }
}

// Include the header AFTER all PHP logic
include_once 'header.php';
?>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2><i class="fa fa-list-alt"></i> Manage Quizzes</h2>
        <a href="addq.php" class="btn btn-solid"><i class="fa fa-plus"></i> Create New Quiz</a>
    </div>
    
    <?php if(isset($delete_error)): ?>
        <div class="card" style="background-color: #991b1b; color: #fee2e2; padding: 1rem; margin-bottom: 1rem;">
            <?php echo $delete_error; ?>
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
                    if (isset($conn)) {
                        $sql = "SELECT * FROM quiz ORDER BY quizid DESC";
                        $res = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                $quiz_id = htmlspecialchars($row['quizid']);
                                $quiz_name = htmlspecialchars($row['quizname']);
                                
                                echo "<tr>
                                        <td>{$quiz_id}</td>
                                        <td>{$quiz_name}</td>
                                        <td style='text-align: right;'>
                                            <a href='addqs.php?q={$quiz_id}' class='btn' style='margin-right: 0.5rem;'>Add/View Q's</a>
                                            <form method='POST' action='quizlist.php' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this quiz? This action cannot be undone.\");'>
                                                <input type='hidden' name='quizid_to_delete' value='{$quiz_id}'>
                                                <button type='submit' name='delete_quiz' class='btn' style='background-color:#991b1b; border-color:#991b1b; color:#fee2e2;'>Delete</button>
                                            </form>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' style='text-align:center; color:var(--text-secondary);'>No quizzes have been created yet.</td></tr>";
                        }
                        mysqli_close($conn);
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; color:#f87171;'>{$db_error}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>