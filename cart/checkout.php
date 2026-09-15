<?php

 
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';
require_once __DIR__ . "/../newsletter-subscribe.php";

$user_id = (int)$_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| GET CART ITEMS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        c.id AS cart_id,
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


/*
|--------------------------------------------------------------------------
| REDIRECT IF CART IS EMPTY
|--------------------------------------------------------------------------
*/

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| CALCULATE TOTAL
|--------------------------------------------------------------------------
*/

$subtotal = 0;

foreach ($cart_items as $item) {
    $subtotal +=
        (float)$item["price"] *
        (int)$item["quantity"];
}

$total = $subtotal;


/*
|--------------------------------------------------------------------------
| GET DEFAULT ADDRESS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        userID,
        full_name,
        phone,
        address_line,
        barangay,
        city,
        province,
        postal_code
    FROM addresses
    WHERE user_id = ?
    AND is_default = 1
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$address = null;

if ($result->num_rows > 0) {
    $address = $result->fetch_assoc();
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| GET DEFAULT PAYMENT METHOD
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        userID,
        payment_type,
        account_name,
        account_number
    FROM payment_methods
    WHERE user_id = ?
    AND is_default = 1
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$payment = null;

if ($result->num_rows > 0) {
    $payment = $result->fetch_assoc();
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pawprint</title>

    <link rel="stylesheet" href="checkout.css">

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
                <a href="cart.php">
                    <img src="../images/cart.png" alt="Cart">
                </a>
                <a href="../account/account.php">
                    <img src="../images/acc.png" alt="Account">
                </a>
            </div>
        </div>
    </header>

    <main class="checkout-page">

            <div class="checkout-container">


                <div class="checkout-header">

                    <h1>Checkout</h1>

                    <p>
                        Review your order before placing it.
                    </p>

                </div>


                <div class="checkout-layout">


                    <div class="checkout-left">


                        <!-- ORDER ITEMS -->

                        <div class="checkout-box">

                            <h2>Your Order</h2>

                            <?php foreach ($cart_items as $item): ?>

                                <div class="checkout-item">

                                    <div class="checkout-item-image">

                                        <?php
                                        if (
                                            !empty($item["image"]) &&
                                            file_exists(__DIR__ . "/../" . ltrim($item["image"], "/"))
                                        ) {
                                            $checkout_image = "../" . ltrim($item["image"], "/");
                                        } else {
                                            $checkout_image = "../images/petfood.png";
                                        }
                                        ?>

                                        <img
                                            src="<?php echo htmlspecialchars($checkout_image); ?>"
                                            alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                        >

                                    </div>


                                    <div class="checkout-item-info">

                                        <h3>
                                            <?php
                                            echo htmlspecialchars($item["name"]);
                                            ?>
                                        </h3>

                                        <p>
                                            Quantity:
                                            <?php
                                            echo (int)$item["quantity"];
                                            ?>
                                        </p>

                                    </div>


                                    <div class="checkout-item-price">

                                        ₱<?php
                                        echo number_format(
                                            $item["price"] * $item["quantity"],
                                            2
                                        );
                                        ?>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>


                        <!-- DELIVERY ADDRESS -->

                        <div class="checkout-box">

                            <h2>Delivery Address</h2>

                            <?php if ($address): ?>

                                <div class="address-details">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $address["full_name"]
                                        );
                                        ?>
                                    </strong>

                                    <br>

                                    <?php
                                    echo htmlspecialchars(
                                        $address["phone"]
                                    );
                                    ?>

                                    <br>

                                    <?php
                                    echo htmlspecialchars(
                                        $address["address_line"]
                                    );
                                    ?>

                                    <br>

                                    <?php
                                    echo htmlspecialchars(
                                        $address["barangay"]
                                    );
                                    ?>,

                                    <?php
                                    echo htmlspecialchars(
                                        $address["city"]
                                    );
                                    ?>,

                                    <?php
                                    echo htmlspecialchars(
                                        $address["province"]
                                    );
                                    ?>

                                    <?php
                                    echo htmlspecialchars(
                                        $address["postal_code"]
                                    );
                                    ?>

                                </div>

                                <a
                                    href="../address/addresses.php"
                                    class="change-link"
                                >
                                    Change Address
                                </a>

                            <?php else: ?>

                                <div class="no-details">

                                    You don't have a default delivery address.

                                </div>

                                <a
                                    href="../address/add-address.php"
                                    class="change-link"
                                >
                                    Add an Address
                                </a>

                            <?php endif; ?>

                        </div>


                        <!-- PAYMENT -->

                        <div class="checkout-box">

                            <h2>Payment Method</h2>

                            <?php if ($payment): ?>

                                <div class="payment-details">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $payment["payment_type"]
                                        );
                                        ?>
                                    </strong>

                                    <?php if (!empty($payment["account_name"])): ?>

                                        <br>

                                        <?php
                                        echo htmlspecialchars(
                                            $payment["account_name"]
                                        );
                                        ?>

                                    <?php endif; ?>

                                    <?php if (!empty($payment["account_number"])): ?>

                                        <br>

                                        <?php
                                        echo htmlspecialchars(
                                            $payment["account_number"]
                                        );
                                        ?>

                                    <?php endif; ?>

                                </div>

                                <a
                                    href="../payment method/payment-method.php"
                                    class="change-link"
                                >
                                    Change Payment Method
                                </a>

                            <?php else: ?>

                                <div class="no-details">

                                    You don't have a default payment method.

                                </div>

                                <a
                                    href="../payment method/add-payment-method.php"
                                    class="change-link"
                                >
                                    Add a Payment Method
                                </a>

                            <?php endif; ?>

                        </div>


                    </div>


                    <!-- SUMMARY -->

                    <aside class="checkout-summary">

                        <h2>Order Summary</h2>


                        <div class="summary-row">

                            <span>Subtotal</span>

                            <strong>
                                ₱<?php echo number_format($subtotal, 2); ?>
                            </strong>

                        </div>


                        <div class="summary-row">

                            <span>Shipping</span>

                            <strong>
                                ₱0.00
                            </strong>

                        </div>


                        <div class="summary-divider"></div>


                        <div class="summary-total">

                            <span>Total</span>

                            <strong>
                                ₱<?php echo number_format($total, 2); ?>
                            </strong>

                        </div>


                        <?php if ($address && $payment): ?>

                            <button
                                type="button"
                                class="place-order-btn"
                                onclick="placeOrder()"
                            >
                                Place Order
                            </button>

                        <?php else: ?>

                            <button
                                type="button"
                                class="place-order-btn"
                                disabled
                                style="opacity: .5; cursor: not-allowed;"
                            >
                                Complete Details First
                            </button>

                        <?php endif; ?>


                        <a
                            href="cart.php"
                            class="back-cart"
                        >
                            ← Back to Cart
                        </a>

                    </aside>


                </div>

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

<script src="checkout.js"></script>


</body>

</html>