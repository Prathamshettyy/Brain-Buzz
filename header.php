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
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">Brain-Buzz</a>
            </div>
            <nav class="main-nav">
                <ul>
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
                        <li><a href="contact.php">Contact</a></li> 
                        <li><a href="login.php" class="btn">Login</a></li>
                        <li><a href="signup.php" class="btn">Sign Up</a></li> 
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">