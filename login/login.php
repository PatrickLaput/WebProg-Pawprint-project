<?php
require_once __DIR__ . "/../newsletter-subscribe.php";
?>

<DOCTYPE html>
<html>

    <head>
            <title>Pawprint</title>
            <link rel="stylesheet" type="text/css" href="login.css">
    </head>

    <body>

        <script src="login.js"></script>
        
        <header class="site-header">
            <div class="nav">

                <div class="logo">
                    <a href="../index.php">
                        <img src="../images/logo.png" alt="Pawprint Logo">
                    </a>
                </div>

                <nav class="nav-links">
                    <a href="../index.php">Home</a>
                    <a href="../shop/shop.php">Shop</a>
                    <a href="../about us/about-us.php">About Us</a>
                    <a href="../contact us/contact.php">Contact</a>
                </nav>

                <div class="nav-icons">
                    <a href="../cart/cart.php">
                        <img src="../images/cart.png" alt="Cart">
                    </a>
                    <a href="../account/account.php">
                        <img src="../images/acc.png" alt="Account">
                    </a>
                </div>
            </div>
        </header>

        <section class="login-section">

            <div class="login-card">

                <div class="login-card-header">
                    <img src="../images/pawprint.png" alt="Paw" class="login-paw">
                    <h1>Log In</h1>
                    <p>Good to see you again!</p>
                </div>

                <form id="loginForm" class="login-form" action="log-in.php" method="POST">
                    <div class="login-input">
                        <input type="email" name="email" placeholder="Email Address">
                    </div>

                    <div class="login-input">
                        <input  type="password" name="password" placeholder="Password">
                        <button type="button" class="password-toggle">
                            👁
                        </button>
                    </div>

                    <div class="login-options">
                        <label>
                            <input type="checkbox">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <p id="loginError" class="login-error"></p>
                    <button type="submit" class="signin-button">
                        Log In
                        <span>></span>
                    </button>
                    
                    <p class="signup-text">
                        Don't have an account?
                        <a href="../signup/signup.php">Sign Up</a>
                    </p>
                </form>
            </div>
        </section>

        <footer class="footer">

            <div class="footer-container">
                <div class="footer-brand">

                    <img src="../images/logo-white.png" alt="Pawprint" class="footer-logo">
                    <p>
                        Quality pet food, toys, and accessories<br>
                        made for happy pets and happier<br>
                        pet parents.
                    </p>

                    <div class="footer-socials">
                        <a href="https://youtube.com" aria-label="YouTube">
                            <img src="../images/yt-ico.png" alt="YouTube">
                        </a>
                        <a href="https://facebook.com" aria-label="Facebook">
                            <img src="../images/fb-ico.png" alt="Facebook">
                        </a>
                        <a href="https://instagram.com" aria-label="Instagram">
                            <img src="../images/ig-ico.png" alt="Instagram">
                        </a>
                        <a href="https://tiktok.com" aria-label="TikTok">
                            <img src="../images/tk-ico.png" alt="TikTok">
                        </a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <a href="../shop/shop.php">Shop</a>
                    <a href="../about us/about-us.php">About Us</a>
                    <a href="../contact us/contact.php">Contact Us</a>
                </div>

                <div class="footer-column">
                    <h3>Customer Care</h3>
                    <a href="../account/account.php">My Account</a>
                    <a href="../terms&privacy/terms&privacy.php">Terms & Conditions</a>
                    <a href="../terms&privacy/terms&privacy.php">Privacy Policy</a>
                </div>

                <div class="footer-newsletter">
                    <h3>Stay in the Loop</h3>
                    <p>
                        Get updates on new products,<br>
                        exclusive deals, and pet care tips!
                    </p>
                    <form class="subscribe-form" method="POST">
                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email address"
                            required
                        >

                        <button type="submit">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <img src="../images/pawprint-brown.png" alt="" class="footer-paw">

        </footer>

    </body>
</html>