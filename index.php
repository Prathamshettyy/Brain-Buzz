<?php
// The index page doesn't require session logic at the top,
// so we can just include the header directly.
include_once 'header.php';
?>

<style>
    /* --- Styles specific to the Index Page --- */

    /* Hero Section */
    .hero-section {
        text-align: center;
        padding: 6rem 1rem;
        background: linear-gradient(rgba(18, 18, 18, 0.8), rgba(18, 18, 18, 0.9)), url('assets/img/hero-bg.jpg'); /* Optional: Add a subtle background image */
        background-size: cover;
        background-position: center;
        border-radius: 8px;
        margin-bottom: 4rem;
    }
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff; /* White text for high contrast */
    }
    .hero-section h1 .highlight {
        color: var(--primary-color); /* Use the theme's primary blue */
    }
    .hero-section p {
        font-size: 1.25rem;
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto 2rem auto;
    }
    .hero-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    /* Features Section */
    .features-section {
        padding: 2rem 0;
        text-align: center;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    .feature-card {
        background-color: var(--surface-color);
        padding: 2.5rem 2rem;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    }
    .feature-card .icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
    }
    .feature-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .feature-card p {
        color: var(--text-secondary);
    }

</style>

<div class="container">

    <section class="hero-section">
        <h1>Welcome to <span class="highlight">Brain-Buzz</span></h1>
        <p>The modern, engaging platform for online quizzes. Sharpen your knowledge, challenge your peers, and track your success.</p>
        <div class="hero-actions">
            <a href="signup.php" class="btn btn-solid">Get Started Now</a>
            <a href="login.php" class="btn">I Already Have an Account</a>
        </div>
    </section>

    <section class="features-section">
        <h2>Why Choose Brain-Buzz?</h2>
        <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">Everything you need for a seamless quiz experience.</p>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon"><i class="fa fa-user-graduate"></i></div>
                <h3>For Students</h3>
                <p>Take a wide variety of quizzes, get instant results, and track your scores over time to see your progress.</p>
            </div>

            <div class="feature-card">
                <div class="icon"><i class="fa fa-user-tie"></i></div>
                <h3>For Staff</h3>
                <p>Easily create, manage, and deploy quizzes. Monitor student performance with detailed analytics and leaderboards.</p>
            </div>

            <div class="feature-card">
                <div class="icon"><i class="fa fa-desktop"></i></div>
                <h3>Modern Interface</h3>
                <p>Enjoy our clean, responsive, and dark-themed UI that works beautifully on any device, from desktop to mobile.</p>
            </div>
        </div>
    </section>

</div>

<?php
// Finally, include the footer to close the page
include_once 'footer.php';
?>