<?php

require_once __DIR__ . "/../newsletter-subscribe.php";
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = (int)$_SESSION["user_id"];


/* =========================================================
   GET CART ITEMS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        c.id AS cart_id,
        c.product_id,
        c.quantity,
        p.prod_id,
        p.name,
        p.price,
        p.image,
        p.stock
    FROM cart_items c
    INNER JOIN products p
        ON c.product_id = p.prod_id
    WHERE c.user_id = ?
    ORDER BY c.id DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$stmt->close();


/* =========================================================
   TOTAL
========================================================= */

$subtotal = 0;

foreach ($cart_items as $item) {

    $subtotal +=
        $item["price"] * $item["quantity"];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pawprint</title>

    <link rel="stylesheet" href="cart.css">

</head>

<body>

<script src="cart.js"></script>

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
            <a href="cart.php">
                <img src="../images/cart.png" alt="Cart">
            </a>
            <a href="../account/account.php">
                <img src="../images/acc.png" alt="Account">
            </a>
        </div>
    </div>
</header>

<main class="cart-page">

    <div class="cart-container">


        <div class="cart-header">

            <h1>
                Your Cart
            </h1>

            <p>
                Review the products you've added.
            </p>

        </div>


        <?php if (empty($cart_items)): ?>


            <!-- =================================================
                 EMPTY CART
            ================================================== -->

            <div class="empty-cart">

                <div class="empty-cart-icon">
                    🛒
                </div>

                <h2>
                    Your cart is empty
                </h2>

                <p>
                    You haven't added any products yet.
                </p>

                <a
                    href="../shop/shop.php"
                    class="continue-shopping"
                >
                    Continue Shopping
                </a>

            </div>


        <?php else: ?>


            <div class="cart-layout">


                <!-- =================================================
                     CART ITEMS
                ================================================== -->

                <div class="cart-items">


                    <?php foreach ($cart_items as $item): ?>


                        <div class="cart-item">


                            <!-- PRODUCT IMAGE -->

                            <div class="cart-product-image">

                                <?php
                                if (
                                    !empty($item["image"]) &&
                                    file_exists(__DIR__ . "/../" . ltrim($item["image"], "/"))
                                ) {
                                    $cart_image = "../" . ltrim($item["image"], "/");
                                } else {
                                    $cart_image = "../images/petfood.png";
                                }
                                ?>

                                <img
                                    src="<?php echo htmlspecialchars($cart_image); ?>"
                                    alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                >

                            </div>


                            <!-- PRODUCT INFORMATION -->

                            <div class="cart-product-info">

                                <h2>

                                    <?php
                                    echo htmlspecialchars(
                                        $item["name"]
                                    );
                                    ?>

                                </h2>


                                <p class="cart-price">

                                    ₱<?php
                                    echo number_format(
                                        $item["price"],
                                        2
                                    );
                                    ?>

                                </p>


                                <p class="cart-stock">

                                    <?php if ($item["stock"] > 0): ?>

                                        In Stock

                                    <?php else: ?>

                                        Out of Stock

                                    <?php endif; ?>

                                </p>


                                <!-- QUANTITY -->

                                <div class="quantity-form">

                                    <label>
                                        Quantity
                                    </label>

                                    <input
                                        type="number"
                                        class="quantity-input"
                                        data-cart-id="<?php echo (int)$item["cart_id"]; ?>"
                                        data-price="<?php echo (float)$item["price"]; ?>"
                                        min="1"
                                        max="<?php echo (int)$item["stock"]; ?>"
                                        value="<?php echo (int)$item["quantity"]; ?>"
                                    >

                                </div>


                                <!-- REMOVE -->

                                <a
                                    href="remove-from-cart.php?id=<?php echo (int)$item["cart_id"]; ?>"
                                    class="remove-cart-item"
                                    data-cart-id="<?php echo (int)$item["cart_id"]; ?>"
                                >
                                    Remove
                                </a>

                            </div>


                            <!-- ITEM TOTAL -->

                            <div
                                class="cart-item-total"
                                data-price="<?php echo (float)$item["price"]; ?>"
                            >
                                ₱<?php
                                echo number_format(
                                    $item["price"] * $item["quantity"],
                                    2
                                );
                                ?>
                            </div>


                        </div>


                    <?php endforeach; ?>


                    <a
                        href="../shop/shop.php"
                        class="continue-shopping-link"
                    >
                        ← Continue Shopping
                    </a>


                </div>


                <!-- =================================================
                     ORDER SUMMARY
                ================================================== -->

                <aside class="cart-summary">

                    <h2>
                        Order Summary
                    </h2>


                    <div class="summary-row">

                        <span>
                            Subtotal
                        </span>

                        <strong>

                            ₱<?php
                            echo number_format(
                                $subtotal,
                                2
                            );
                            ?>

                        </strong>

                    </div>


                    <div class="summary-row">

                        <span>
                            Shipping
                        </span>

                        <strong>
                            ₱0.00
                        </strong>

                    </div>


                    <div class="summary-divider"></div>


                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <strong>

                            ₱<?php
                            echo number_format(
                                $subtotal,
                                2
                            );
                            ?>

                        </strong>

                    </div>


                    <a
                        href="checkout.php"
                        class="checkout-btn"
                    >
                        Proceed to Checkout
                    </a>

                </aside>


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