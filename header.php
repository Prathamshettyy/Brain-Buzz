<?php
// Check if a session is not already active before starting one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="assets/img/sah.png" />
    <title>Brain-Buzz</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    <link href="style.css" rel="stylesheet" />
<style>
    .nav-toggle {
    display: none; /* Hidden by default on large screens */
    background: none;
    border: 2px solid var(--text-secondary);
    border-radius: 5px;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
}
.nav-toggle .hamburger {
    display: block;
    width: 25px;
    height: 3px;
    background-color: var(--text-secondary);
    margin: 5px 0;
    transition: 0.4s;
}

@media (max-width: 768px) {
    /* Keep Brain-Buzz & toggle button in one row */
    .site-header .container {
        display: flex;
        flex-direction: row; /* force side-by-side */
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .logo {
        flex: 0 0 auto;
    }

    .nav-toggle {
        display: block;
        flex: 0 0 auto;
        margin-left: auto; /* push to far right */
    }
 @media (max-width: 768px) {
    .main-nav {
        position: relative;
    }

    .main-nav ul {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 60px;
        right: 0;
        width: auto;
        min-width: 160px; /* fixed equal width for all buttons */
        background: rgba(5, 5, 5, 0.95);
        border-radius: 10px;
        padding: 0.5rem;
        margin: 0;
        list-style: none;
        box-shadow: 0 6px 16px rgba(60, 95, 136, 0.25);
        animation: slideDown 0.3s ease forwards;
        z-index: 1000;
        align-items: center; /* center the buttons */
    }

    .main-nav ul.active {
        display: flex;
    }

    .main-nav ul li {
        width: 100%;
    }

    .main-nav ul li a {
        display: block;
        width: 140px; /* equal size for all buttons */
        padding: 0.6rem 0;
        font-size: 0.9rem;
        color: #fff;
        text-align: center;
        text-decoration: none;
        border-radius: 6px;
        background: rgba(40, 40, 40, 0.9);
        transition: background 0.2s ease;
    }

    .main-nav ul li + li {
        margin-top: 0.4rem;
    }

    .main-nav ul li a:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
}


</style>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">Brain-Buzz</a>
            </div>
            
            <nav class="main-nav">
                <button class="nav-toggle" id="nav-toggle-button" aria-label="Toggle navigation">
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                    <span class="hamburger"></span>
                </button>
                <ul id="nav-menu">
                    <?php if (isset($_SESSION['email']) || isset($_SESSION['usn']) || isset($_SESSION['staffid'])) : ?>
                        
                        <?php if (isset($_SESSION['acc_type']) && $_SESSION['acc_type'] == 'staff') : ?>
                            <li><a href="homestaff.php">Dashboard</a></li>
                            <li><a href="staffprofile.php">Profile</a></li>
                        <?php else : ?>
                            <li><a href="homestud.php">Dashboard</a></li>
                            <li><a href="studprofile.php">Profile</a></li>
                        <?php endif; ?>

                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="logout.php">Logout</a></li>

                    <?php else : // User is logged out ?>
                        <li><a href="contact.php" class="btn">Contact</a></li> 
                        <li><a href="login.php" class="btn">Login</a></li>
                        <li><a href="signup.php" class="btn">Sign Up</a></li> 
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">

    <script>
        const navToggleButton = document.getElementById('nav-toggle-button');
        const navMenu = document.getElementById('nav-menu');

        navToggleButton.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    </script>
