<?php
require_once __DIR__ . "/../newsletter-subscribe.php";
?>

<DOCTYPE html>
<html>

    <head>
            <title>Pawprint</title>
            <link rel="stylesheet" type="text/css" href="signup.css">
    </head>

    <body>

        <script src="signup.js"></script>
        
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

        <section class="signup-section">

            <div class="signup-card">

                <div class="signup-header">
                    <img src="../images/pawprint.png" alt="Paw">
                    <h1>Sign Up</h1>
                    <p>
                        Fill in the details below to create your Pawprint account.
                    </p>
                </div>

                <form id="signup-form" action="sign-up.php" method="POST">

                    <div class="signup-field">
                        <label>Email Address *</label>
                        <div class="signup-input">
                            <input 
                                type="email" 
                                name="email" 
                                placeholder="e.g. johndoe@example.com" 
                                required>
                        </div>
                    </div>

                    <div class="signup-field">
                        <label>First Name *</label>
                        <div class="signup-input">
                            <input 
                                type="text" 
                                name="first_name" 
                                placeholder="e.g. John" 
                                required>
                        </div>
                    </div>

                    <div class="signup-field">
                        <label>Last Name *</label>
                        <div class="signup-input">
                            <input 
                                type="text" 
                                name="last_name" 
                                placeholder="e.g. Doe" 
                                required>
                        </div>
                    </div>

                    <div class="signup-field">
                        <label>Birthdate *</label>
                        <div class="signup-input"> 
                            <input
                                type="date"
                                id="birthdate"
                                name="birthdate"
                                required>
                        </div>   
                    </div>

                    <div class="signup-field">
                        <label for="password">Password *</label>

                        <div class="signup-input">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Create a password"
                                required>
                            <button type="button" class="password-toggle">👁</button>
                        </div>
                    </div>


                    <div class="signup-field">
                        <label for="confirm-password">Confirm Password *</label>

                        <div class="signup-input">
                            <input
                                type="password"
                                id="confirm-password"
                                name="confirm_password"
                                placeholder="Confirm your password"
                                required>
                            <button type="button" class="password-toggle">👁</button>
                        </div>

                        <small id="password-error"></small>
                    </div>

                    <div class="terms">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">
                            I agree to the
                            <a href="../terms&privacy/terms&privacy.php">Terms &amp; Conditions</a>
                            and
                            <a href="../terms&privacy/terms&privacy.php">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit" class="create-account-button">Create Account
                        <span class="button-arrow">></span>
                    </button>

                    <p class="login-link">
                        Already have an account?
                        <a href="../login/login.php">Log In</a>
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