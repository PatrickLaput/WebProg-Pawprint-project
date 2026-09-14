<?php
require_once __DIR__ . "/../newsletter-subscribe.php";

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if (
        empty($full_name) ||
        empty($email) ||
        empty($subject) ||
        empty($message)
    ) {
        $error_message = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {

        $messages_folder = __DIR__ . "/messages";

        if (!is_dir($messages_folder)) {
            mkdir($messages_folder, 0755, true);
        }

        $message_data =
            "Date: " . date("Y-m-d H:i:s") . PHP_EOL .
            "Full Name: " . $full_name . PHP_EOL .
            "Email: " . $email . PHP_EOL .
            "Phone: " . $phone . PHP_EOL .
            "Subject: " . $subject . PHP_EOL .
            "Message: " . $message . PHP_EOL .
            str_repeat("-", 60) . PHP_EOL;

        $file_path = $messages_folder . "/contact-messages.txt";

        if (file_put_contents($file_path, $message_data, FILE_APPEND | LOCK_EX)) {
            $success_message = "Your message has been sent successfully.";
        } else {
            $error_message = "There was a problem sending your message.";
        }
    }
}
?>

<DOCTYPE html>
<html>

    <head>
            <title>Pawprint</title>
            <link rel="stylesheet" type="text/css" href="contact.css">
    </head>

    <body>
        
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
                    <a href="contact.php">Contact</a>
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

        <section class="contact-hero">

            <div class="contact-hero-content">
                <h1>
                    Get in Touch,<br>
                    <span>We’re Here to Help!</span>
                </h1>
                <p class="contact-hero-description">
                    Have a question, suggestion, or need support?
                    Our team is always happy to assist you and
                    your furry friend.
                </p>

                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon email-icon">
                            <img src="../images/mail-ico.png" alt="Email">
                        </div>

                        <div>
                            <h3>Email Us</h3>
                            <p>hello@pawprint.com</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon phone-icon">
                            <img src="../images/telephone-ico.png" alt="Phone">
                        </div>

                        <div>
                            <h3>Call Us</h3>
                            <p>+63 912 345 6789</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon hours-icon">
                            <img src="../images/clock-ico.png" alt="Opening hours">
                        </div>

                        <div>
                            <h3>We're Open</h3>
                            <p>Mon – Sun, 9AM – 6PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-hero-image">
                <img src="../images/contact-hero.png" alt="Dog and cat">
            </div>
        </section>

        <section class="contact-details">

            <div class="contact-details-container">

                <div class="contact-form-box">

                    <div class="section-title">
                        <img src="../images/pawprint.png" alt="">
                        <h2>Send Us a Message</h2>
                    </div>

                    <p class="section-description">
                        Fill out the form below and we'll get back to you as soon as possible.
                    </p>

                    <?php if (!empty($success_message)): ?>
                        <p class="success-message">
                            <?php echo htmlspecialchars($success_message); ?>
                        </p>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <p class="error-message">
                            <?php echo htmlspecialchars($error_message); ?>
                        </p>
                    <?php endif; ?>

                    <form class="contact-form" action="contact.php" method="POST">

                        <div class="form-row">
                            <input
                                type="text"
                                name="full_name"
                                placeholder="Full Name"
                                required
                            >

                            <input
                                type="email"
                                name="email"
                                placeholder="Email Address"
                                required
                            >
                        </div>

                        <input
                            type="tel"
                            name="phone"
                            placeholder="Phone Number"
                        >

                        <div class="select-wrapper">
                            <select name="subject" required>
                                <option value="" selected disabled>
                                    Subject
                                </option>

                                <option value="Order Inquiry">Order Inquiry</option>
                                <option value="Product Inquiry">Product Inquiry</option>
                                <option value="Customer Support">Customer Support</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <textarea
                            name="message"
                            placeholder="Message"
                            required
                        ></textarea>

                        <button type="submit">
                            <img src="../images/pawprint-white.png" alt="">
                            Send Message
                        </button>

                    </form>
                </div>

                <div class="contact-information">

                    <div class="section-title">
                        <img src="../images/pawprint.png" alt="">
                        <h2>Contact Information</h2>
                    </div>

                    <p class="section-description">
                        Choose the way that works best for you.
                    </p>

                    <div class="contact-detail-item">
                        <div class="detail-icon address-icon">
                            <img src="../images/location-ico.png" alt="Address">
                        </div>

                        <div class="detail-text">
                            <h3>Our Address</h3>
                            <p>
                                Pawprint Pet Supplies<br>
                                123 Pawprint Lane<br>
                                Quezon City, Philippines 1100
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail-item">
                        <div class="detail-icon email-icon">
                            <img src="../images/mail-ico.png" alt="Email">
                        </div>
                        <div class="detail-text">
                            <h3>Email Us</h3>
                            <p>
                                hello@pawprint.com<br>
                                support@pawprint.com
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail-item">
                        <div class="detail-icon phone-icon">
                            <img src="../images/telephone-ico.png" alt="Phone">
                        </div>

                        <div class="detail-text">
                            <h3>Call Us</h3>
                            <p>
                                +63 912 345 6789<br>
                                +63 998 765 4321
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail-item">
                        <div class="detail-icon hours-icon">
                            <img src="../images/clock-ico.png" alt="Business Hours">
                        </div>
                        <div class="detail-text">
                            <h3>Business Hours</h3>
                            <p>
                                Monday – Sunday<br>
                                9:00 AM – 6:00 PM
                            </p>
                        </div>
                    </div>

                    <div class="contact-detail-item">
                        <div class="detail-icon chat-icon">
                            <img src="../images/chat-ico.png" alt="Live Chat">
                        </div>

                        <div class="detail-text">
                            <h3>Live Chat</h3>
                            <p>
                                Chat with us on our website<br>
                                Mon – Sun, 9AM – 6PM
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="help-section">

            <div class="help-container">

                <div class="help-header">
                    <div class="help-title">
                        <img src="../images/pawprint.png" alt="Paw">
                        <h2>Need Help With?</h2>
                    </div>
                    <p>We're here to make your experience easy and enjoyable.</p>
                </div>

                <div class="help-categories">
                    <div class="help-item">
                        <div class="help-icon order-icon">
                            <img src="../images/bag.png" alt="Order Inquiries">
                        </div>

                        <h3>Order Inquiries</h3>
                        <p>Questions about your<br>orders or tracking?</p>
                    </div>

                    <div class="help-item">
                        <div class="help-icon product-icon">
                            <img src="../images/box.png" alt="Product Information">
                        </div>
                        <h3>Product Information</h3>
                        <p>Need help choosing<br>the right product?</p>
                    </div>

                    <div class="help-item">
                        <div class="help-icon returns-icon">
                            <img src="../images/undo.png" alt="Returns & Refunds">
                        </div>
                        <h3>Returns & Refunds</h3>
                        <p>Inquiries about returns<br>or exchanges?</p>
                    </div>

                    <div class="help-item">
                        <div class="help-icon account-icon">
                            <img src="../images/person.png" alt="Account Support">
                        </div>
                        <h3>Account Support</h3>
                        <p>Need help with your<br>account or password?</p>
                    </div>

                    <div class="help-item">

                        <div class="help-icon concerns-icon">
                            <img src="../images/heart.png" alt="Other Concerns">
                        </div>
                        <h3>Other Concerns</h3>
                        <p>We're here for any other<br>questions you have.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-cta">

            <div class="contact-cta-container">

                <div class="contact-cta-dog">
                    <img src="../images/corgi.png" alt="Happy dog">
                </div>

                <div class="contact-cta-icon">
                    <img src="../images/pawprint-white.png" alt="Paw">
                </div>

                <div class="contact-cta-content">
                    <h2>
                        Your Pet’s Happiness<br>
                        <span>is Our Priority</span>
                    </h2>
                    <p>
                        Thank you for being part of the Pawprint family.<br>
                        We can’t wait to hear from you!
                    </p>
                </div>
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
                    <a href="contact.php">Contact Us</a>
                </div>

                <div class="footer-column">
                    <h3>Customer Care</h3>
                    <a href="../account/account.php">My Account</a>
                    <a href="../terms&privacy.php">Terms & Conditions</a>
                    <a href="../terms&privacy.php">Privacy Policy</a>
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