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
            .main-nav ul {
                display: none; /* Hide the nav links by default on mobile */
                flex-direction: column;
                position: absolute;
                top: 70px; /* Position below the header */
                left: 0;
                width: 100%;
                background-color: var(--surface-color);
                padding: 1rem 0;
            }
            .main-nav ul.active {
                display: flex; /* Show the nav links when the menu is active */
            }
            .main-nav ul li {
                width: 100%;
                text-align: center;
            }
            .main-nav ul li a {
                padding: 1rem;
                display: block; /* Make the whole area clickable */
            }
            .nav-toggle {
                display: block; /* Show the hamburger button on mobile */
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