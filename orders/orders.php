<?php

 

require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = (int)$_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| GET ORDERS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        order_id,
        total_amount,
        status,
        payment_type,
        created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pawprint</title>

    <link rel="stylesheet" href="orders.css">


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

    <main class="orders-page">

        <div class="orders-container">


            <div class="orders-header">
                <a
                    href="../account/account.php"
                    class="back-link"
                >
                    ← Back to Account
                </a>
                <h1>My Orders</h1>

                <p>
                    View your previous and current orders.
                </p>

            </div>


            <?php if (empty($orders)): ?>


                <div class="empty-orders">

                    <h2>No Orders Yet</h2>

                    <p>
                        You haven't placed any orders yet.
                    </p>

                    <a
                        href="../shop/shop.php"
                        class="shop-button"
                    >
                        Start Shopping
                    </a>

                </div>


            <?php else: ?>


                <div class="orders-list">


                    <?php foreach ($orders as $order): ?>

                        <div class="order-card">


                            <div class="order-top">

                                <div>

                                    <div class="order-number">

                                        Order #<?php
                                        echo (int)$order["order_id"];
                                        ?>

                                    </div>

                                    <div class="order-date">

                                        <?php
                                        echo date(
                                            "F j, Y",
                                            strtotime(
                                                $order["created_at"]
                                            )
                                        );
                                        ?>

                                    </div>

                                </div>


                                <span class="order-status">

                                    <?php
                                    echo htmlspecialchars(
                                        $order["status"]
                                    );
                                    ?>

                                </span>

                            </div>


                            <div class="order-info">

                                <div class="order-payment">

                                    Payment:
                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $order["payment_type"]
                                        );
                                        ?>
                                    </strong>

                                </div>


                                <div class="order-total">

                                    Total:

                                    <span>
                                        ₱<?php
                                        echo number_format(
                                            $order["total_amount"],
                                            2
                                        );
                                        ?>
                                    </span>

                                </div>

                            </div>


                        </div>

                    <?php endforeach; ?>


                </div>


            <?php endif; ?>


        </div>

    </main>

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
                <a href="../terms&privacy.php">Terms & Conditions</a>
                <a href="../terms&privacy.php">Privacy Policy</a>
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
        <img src="../images/pawprint-brown.png" alt="" class="footer-paw">

    </footer>


</body>

</html>