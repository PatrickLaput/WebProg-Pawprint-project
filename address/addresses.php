<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT *
    FROM addresses
    WHERE user_id = ?
    ORDER BY is_default DESC, userID DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Pawprint</title>
    <link rel="stylesheet" href="addresses.css">

</head>

<body>

<header class="site-header">
            <div class="nav">

                <div class="logo">
                    <a href="#">
                        <img src="images/logo.png" alt="Pawprint Logo">
                    </a>
                </div>

                <nav class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="shop.php">Shop</a>
                    <a href="about-us.php">About Us</a>
                    <a href="contact.php">Contact</a>
                </nav>

                <div class="nav-icons">
                    <a href="#">
                        <img src="images/search.png" alt="Search">
                    </a>
                    <a href="#">
                        <img src="images/cart.png" alt="Cart">
                    </a>
                    <a href="account.php">
                        <img src="images/acc.png" alt="Account">
                    </a>
                </div>
            </div>
        </header>


<main class="addresses-page">

    <div class="addresses-container">

        <div class="addresses-header">

            <div>
                <a
                    href="account.php"
                    class="back-link"
                >
                    ← Back to Account
                </a>
                <h1>My Addresses</h1>
                <p>Manage your delivery addresses.</p>
            </div>

            <a href="add-address.php" class="add-address-btn">
                + Add New Address
            </a>

        </div>


        <div class="addresses-list">

            <?php if ($result->num_rows > 0): ?>

                <?php while ($address = $result->fetch_assoc()): ?>

                    <div class="address-card">

                        <div class="address-top">

                            <div>
                                <h2>
                                    <?php echo htmlspecialchars($address["full_name"]); ?>
                                </h2>

                                <?php if ($address["is_default"]): ?>
                                    <span class="default-badge">Default</span>
                                <?php endif; ?>
                            </div>

                        </div>


                        <p class="phone">
                            <?php echo htmlspecialchars($address["phone"]); ?>
                        </p>


                        <p class="address-text">
                            <?php echo htmlspecialchars($address["address_line"]); ?><br>

                            <?php echo htmlspecialchars($address["barangay"]); ?>,
                            <?php echo htmlspecialchars($address["city"]); ?><br>

                            <?php echo htmlspecialchars($address["province"]); ?>
                            <?php echo htmlspecialchars($address["postal_code"]); ?>
                        </p>


                        <div class="address-actions">

                            <a
                                href="edit-address.php?userID=<?php echo $address["userID"]; ?>"
                                class="edit-address"
                            >
                                Edit
                            </a>

                            <?php if (!$address["is_default"]): ?>

                                <a
                                    href="set-default-address.php?userID=<?php echo $address["userID"]; ?>"
                                    class="default-address"
                                >
                                    Set as Default
                                </a>

                            <?php endif; ?>

                            <a
                                href="delete-address.php?userID=<?php echo (int)$address["userID"]; ?>"
                                class="delete-address"
                                onclick="return confirm('Are you sure you want to delete this address?');"
                            >
                                Delete
                            </a>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="no-addresses">

                    <h2>No addresses yet</h2>

                    <p>
                        Add an address so you can use it when checking out.
                    </p>

                    <a href="add-address.php" class="add-address-btn">
                        Add Your First Address
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>

    <footer class="footer">

            <div class="footer-container">
                <div class="footer-brand">

                    <img src="images/logo-white.png" alt="Pawprint" class="footer-logo">
                    <p>
                        Quality pet food, toys, and accessories<br>
                        made for happy pets and happier<br>
                        pet parents.
                    </p>

                    <div class="footer-socials">
                        <a href="#" aria-label="YouTube">
                            <img src="images/yt-ico.png" alt="YouTube">
                        </a>
                        <a href="#" aria-label="Facebook">
                            <img src="images/fb-ico.png" alt="Facebook">
                        </a>
                        <a href="#" aria-label="Instagram">
                            <img src="images/ig-ico.png" alt="Instagram">
                        </a>
                        <a href="#" aria-label="TikTok">
                            <img src="images/tk-ico.png" alt="TikTok">
                        </a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <a href="shop.php">Shop</a>
                    <a href="about-us.php">About Us</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="#">FAQs</a>
                </div>

                <div class="footer-column">
                    <h3>Customer Care</h3>
                    <a href="#">My Account</a>
                    <a href="#">Track Order</a>
                    <a href="#">Shipping & Returns</a>
                    <a href="#">Terms & Conditions</a>
                    <a href="#">Privacy Policy</a>
                </div>

                <div class="footer-newsletter">
                    <h3>Stay in the Loop</h3>
                    <p>
                        Get updates on new products,<br>
                        exclusive deals, and pet care tips!
                    </p>
                    <form class="subscribe-form">

                        <input type="email" placeholder="Enter you email" required>
                        <button type="submit">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <img src="images/pawprint-brown.png" alt="" class="footer-paw">

    </footer>

</body>
</html>