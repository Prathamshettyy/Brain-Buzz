</main> <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Brain-Buzz. All Rights Reserved.</p>
            
            <div class="footer-links">
                <span>Connect with me:</span>
                <a href="https://github.com/prathamshettyy" target="_blank" title="GitHub">
                    <i class="fab fa-github"></i>
                </a>
                <a href="https://www.linkedin.com/in/prathamrshetty" target="_blank" title="LinkedIn">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="mailto:prathamshetty329@gmail.com" title="Email">
                    <i class="fa fa-envelope"></i>
                </a>
            </div>
        </div>
    </footer>

    <style>
        .site-footer .container {
            display: flex;
            flex-direction: column; /* Stack items vertically */
            justify-content: center; /* Center vertically */
            align-items: center;     /* Center horizontally */
            gap: 0.5rem;             /* Add a little space between the lines */
        }

        .footer-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-links span {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 1.3rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
            transform: scale(1.1);
        }
    </style>

</body>
</html>