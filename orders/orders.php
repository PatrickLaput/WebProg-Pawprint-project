<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT
        order_id,
        total_amount,
        status,
        payment_type,
        shipping_full_name,
        shipping_address,
        shipping_barangay,
        shipping_city,
        shipping_province,
        shipping_postal_code,
        created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

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

    <a
        href="../account/account.php"
        class="back-link"
    >
        ← Back to Account
    </a>
    
    <h1>My Orders</h1>

    <?php if ($orders_result->num_rows === 0): ?>

        <div class="empty-orders">
            <p>You have not placed any orders yet.</p>
            <a href="../shop.php" class="shop-link">Start Shopping</a>
        </div>

    <?php else: ?>

        <?php while ($order = $orders_result->fetch_assoc()): ?>

            <section class="order-card">

                <div class="order-header">
                    <div>
                        <h2>
                            Order #<?php echo (int) $order["order_id"]; ?>
                        </h2>

                        <div class="order-date">
                            <?php echo date("F j, Y g:i A", strtotime($order["created_at"])); ?>
                        </div>
                    </div>

                    <span class="order-status">
                        <?php echo htmlspecialchars($order["status"]); ?>
                    </span>
                </div>

                <div class="order-details">
                    <strong>Payment Method:</strong>
                    <?php echo htmlspecialchars($order["payment_type"]); ?><br>

                    <strong>Delivery Address:</strong>
                    <?php echo htmlspecialchars($order["shipping_full_name"]); ?>,
                    <?php echo htmlspecialchars($order["shipping_address"]); ?>,
                    <?php echo htmlspecialchars($order["shipping_barangay"]); ?>,
                    <?php echo htmlspecialchars($order["shipping_city"]); ?>,
                    <?php echo htmlspecialchars($order["shipping_province"]); ?>,
                    <?php echo htmlspecialchars($order["shipping_postal_code"]); ?>
                </div>

                <div class="ordered-products">
                    <h3>Ordered Products</h3>

                    <?php
                    $item_stmt = $conn->prepare("
                        SELECT
                            product_name,
                            price,
                            quantity
                        FROM order_items
                        WHERE order_id = ?
                        ORDER BY order_item_id ASC
                    ");

                    $item_stmt->bind_param("i", $order["order_id"]);
                    $item_stmt->execute();
                    $items_result = $item_stmt->get_result();
                    ?>

                    <?php while ($item = $items_result->fetch_assoc()): ?>

                        <div class="ordered-product">
                            <div>
                                <div class="ordered-product-name">
                                    <?php echo htmlspecialchars($item["product_name"]); ?>
                                </div>

                                <div class="ordered-product-info">
                                    ₱<?php echo number_format($item["price"], 2); ?>
                                    ×
                                    <?php echo (int) $item["quantity"]; ?>
                                </div>
                            </div>

                            <div class="ordered-product-total">
                                ₱<?php
                                echo number_format(
                                    $item["price"] * $item["quantity"],
                                    2
                                );
                                ?>
                            </div>
                        </div>

                    <?php endwhile; ?>

                    <?php $item_stmt->close(); ?>
                </div>

                <div class="order-footer">
                    <div class="order-total">
                        Total: ₱<?php echo number_format($order["total_amount"], 2); ?>
                    </div>

                    <?php if (
                        $order["status"] !== "Cancelled" &&
                        $order["status"] !== "Completed" &&
                        $order["status"] !== "Delivered"
                    ): ?>

                        <form
                            method="POST"
                            action="cancel-order.php"
                            onsubmit="return confirm('Are you sure you want to cancel this order?');"
                        >
                            <input
                                type="hidden"
                                name="order_id"
                                value="<?php echo (int) $order["order_id"]; ?>"
                            >

                            <button type="submit" class="cancel-order-btn">
                                Cancel Order
                            </button>
                        </form>

                    <?php elseif ($order["status"] === "Cancelled"): ?>

                        <div class="cancelled-message">
                            This order has been cancelled.
                        </div>

                    <?php endif; ?>
                </div>

            </section>

        <?php endwhile; ?>

    <?php endif; ?>
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