<?php
include_once 'header.php';
?>

<style>
    .login-chooser-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2rem;
        text-align: center;
        padding: 4rem 0;
    }
    .login-card {
        background-color: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 3rem;
        width: 300px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .login-card i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
    }
    .login-card h3 {
        margin-bottom: 1rem;
    }
    .login-card .btn {
        width: 100%;
        margin-top: 1.5rem;
    }

    @media (max-width: 768px) {
        .login-chooser-container {
            flex-direction: column;
        }
    }
</style>

<div class="container">
    <div style="text-align: center; margin: 2rem 0;">
        <h2>How would you like to log in?</h2>
        <p style="color:var(--text-secondary);">Please select your account type below.</p>
    </div>

    <div class="login-chooser-container">
        
        <a href="loginstud.php" style="text-decoration: none;">
            <div class="login-card">
                <i class="fa fa-user-graduate"></i>
                <h3>I am a Student</h3>
                <p style="color:var(--text-secondary);">Access quizzes and view your scores.</p>
                <div class="btn">Login as Student</div>
            </div>
        </a>

        <a href="loginstaff.php" style="text-decoration: none;">
            <div class="login-card">
                <i class="fa fa-user-tie"></i>
                <h3>I am Staff</h3>
                <p style="color:var(--text-secondary);">Manage quizzes and student results.</p>
                <div class="btn">Login as Staff</div>
            </div>
        </a>

    </div>
</div>

<?php
include_once 'footer.php';
?>