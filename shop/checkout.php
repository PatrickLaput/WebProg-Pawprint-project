<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit();
}

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

    <nav class="nav">

        <div class="logo">

            <a href="index.php">

                <img
                    src="images/logo.png"
                    alt="Pawprint"
                >

            </a>

        </div>


        <div class="nav-links">

            <a href="index.php">Home</a>

            <a href="shop.php">Shop</a>

            <a href="about.php">About Us</a>

            <a href="blog.php">Blog</a>

            <a href="contact.php">Contact</a>

        </div>


        <div class="nav-icons">

            <a href="search.php">

                <img
                    src="images/search.png"
                    alt="Search"
                >

            </a>

            <a href="cart.php">

                <img
                    src="images/cart.png"
                    alt="Cart"
                >

            </a>

            <a href="account.php">

                <img
                    src="images/acc.png"
                    alt="Account"
                >

            </a>

        </div>

    </nav>

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

                                <?php if (!empty($item["image"])): ?>

                                    <img
                                        src="<?php echo htmlspecialchars($item["image"]); ?>"
                                        alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                    >

                                <?php else: ?>

                                    <span>🐾</span>

                                <?php endif; ?>

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
                            href="addresses.php"
                            class="change-link"
                        >
                            Change Address
                        </a>

                    <?php else: ?>

                        <div class="no-details">

                            You don't have a default delivery address.

                        </div>

                        <a
                            href="add-address.php"
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
                            href="payment-methods.php"
                            class="change-link"
                        >
                            Change Payment Method
                        </a>

                    <?php else: ?>

                        <div class="no-details">

                            You don't have a default payment method.

                        </div>

                        <a
                            href="add-payment-method.php"
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


<script src="checkout.js"></script>


</body>

</html>