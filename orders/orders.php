<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

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


<main class="orders-page">

    <div class="orders-container">


        <div class="orders-header">
            <a
                href="account.php"
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
                    href="shop.php"
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


</body>

</html>