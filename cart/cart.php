<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit();
}

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

            <a href="index.php">
                Home
            </a>

            <a href="shop.php">
                Shop
            </a>

            <a href="about.php">
                About Us
            </a>

            <a href="blog.php">
                Blog
            </a>

            <a href="contact.php">
                Contact
            </a>

        </div>


        <div class="nav-icons">

            <a href="search.php">

                <img
                    src="images/search.png"
                    alt="Search"
                >

            </a>

            <a
                href="cart.php"
                class="active-cart"
            >

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


<!-- =====================================================
     CART
===================================================== -->

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
                    href="shop.php"
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

                                $image_path =
                                    !empty($item["image"])
                                    ? $item["image"]
                                    : "";

                                ?>

                                <?php if (
                                    $image_path !== "" &&
                                    file_exists($image_path)
                                ): ?>

                                    <img
                                        src="<?php echo htmlspecialchars($image_path); ?>"
                                        alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                    >

                                <?php else: ?>

                                    <div class="cart-placeholder">

                                        <span>🐾</span>

                                    </div>

                                <?php endif; ?>

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
                        href="shop.php"
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


</body>
</html>