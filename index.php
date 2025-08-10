<?php
// The index page doesn't require session logic at the top.
include_once 'header.php';
?>

<style>
    /* --- General Enhancements --- */
    .fade-in-section {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }
    .fade-in-section.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* --- Hero Section Enhancements --- */
    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .hero-section {
        text-align: center;
        padding: 6rem 1rem;
        background: linear-gradient(-45deg, #121212, #1E1E1E, #0ea5e9, #121212);
        background-size: 400% 400%;
        animation: gradient-animation 15s ease infinite;
        border-radius: 8px;
        margin-bottom: 4rem;
    }

    @keyframes fade-in-down {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff;
        animation: fade-in-down 1s ease-out forwards;
    }
    .hero-section h1 .highlight {
        color: var(--primary-color);
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
    .hero-actions .btn {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hero-actions .btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* --- Features Section Enhancements --- */
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
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }
    .feature-card .icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
    }
    .feature-card:hover .icon {
        transform: scale(1.1);
    }
    .feature-card h3 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .feature-card p {
        color: var(--text-secondary);
    }
    /* This is the new hover effect */
.feature-card:hover {
    transform: translateY(-10px);
    /* THIS IS THE LINE I CHANGED */
    box-shadow: 0 20px 40px rgba(56, 189, 248, 0.2); /* A glowing blue shadow */
}
</style>

<div class="container">

    <section class="hero-section fade-in-section">
        <h1>Welcome to <span class="highlight">Brain-Buzz</span></h1>
        <p>The modern, engaging platform for online quizzes. Sharpen your knowledge, challenge your peers, and track your success.</p>
        <div class="hero-actions">
            <a href="signup.php" class="btn btn-solid">Get Started Now</a>
            <a href="login.php" class="btn">I Already Have an Account</a>
        </div>
    </section>

    <section class="features-section fade-in-section">
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
                <div class="icon"><i class="fa fa-magic"></i></div>
                <h3>Intuitive & Engaging</h3>
                <p>A streamlined platform built for a smooth, distraction-free experience for both quiz creators and students.</p>
            </div>
        </div>
    </section>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sections = document.querySelectorAll('.fade-in-section');

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        sections.forEach(section => {
            observer.observe(section);
        });
    });
</script>

<?php
// Finally, include the footer to close the page
include_once 'footer.php';
?>