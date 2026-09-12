<?php

session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT
        userID,
        payment_type,
        account_name,
        account_number,
        is_default
    FROM payment_methods
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

    <link rel="stylesheet" href="payment-method.css">

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



<!-- PAYMENT METHODS -->

<main class="payment-page">

    <div class="payment-container">


        <!-- HEADER -->

        <div class="payment-header">

            <div>
                <a
                    href="account.php"
                    class="back-link"
                >
                    ← Back to Account
                </a>

                <h1>Payment Methods</h1>

                <p>
                    Manage your preferred payment methods.
                </p>

            </div>


            <a
                href="add-payment-method.php"
                class="add-payment-btn"
            >
                + Add Payment Method
            </a>

        </div>



        <!-- PAYMENT LIST -->

        <div class="payment-list">

            <?php if ($result->num_rows > 0): ?>


                <?php while ($payment = $result->fetch_assoc()): ?>

                    <div class="payment-card">


                        <!-- TOP -->

                        <div class="payment-top">

                            <div>

                                <h2>
                                    <?php
                                    echo htmlspecialchars(
                                        $payment["payment_type"]
                                    );
                                    ?>
                                </h2>


                                <?php if ($payment["is_default"]): ?>

                                    <span class="default-badge">
                                        Default
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>



                        <!-- DETAILS -->

                        <?php if (!empty($payment["account_name"])): ?>

                            <p class="account-name">

                                <?php
                                echo htmlspecialchars(
                                    $payment["account_name"]
                                );
                                ?>

                            </p>

                        <?php endif; ?>


                        <?php if (!empty($payment["account_number"])): ?>

                            <p class="account-number">

                                <?php
                                $number = $payment["account_number"];

                                if (strlen($number) > 4) {

                                    echo "••••••••" .
                                         htmlspecialchars(
                                             substr($number, -4)
                                         );

                                } else {

                                    echo htmlspecialchars($number);

                                }
                                ?>

                            </p>

                        <?php endif; ?>



                        <!-- ACTIONS -->

                        <div class="payment-actions">


                            <a
                                href="edit-payment-method.php?userID=<?php echo (int)$payment["userID"]; ?>"
                                class="edit-payment"
                            >
                                Edit
                            </a>


                            <?php if (!$payment["is_default"]): ?>

                                <a
                                    href="set-default-payment.php?userID=<?php echo (int)$payment["userID"]; ?>"
                                    class="default-payment"
                                >
                                    Set as Default
                                </a>

                            <?php endif; ?>


                            <a
                                href="delete-payment-method.php?userID=<?php echo (int)$payment["userID"]; ?>"
                                class="delete-payment"
                                onclick="return confirm('Are you sure you want to delete this payment method?');"
                            >
                                Delete
                            </a>

                        </div>


                    </div>

                <?php endwhile; ?>


            <?php else: ?>


                <!-- NO PAYMENT METHODS -->

                <div class="no-payment-methods">

                    <h2>
                        No payment methods yet
                    </h2>

                    <p>
                        Add a payment method so you can easily use it during checkout.
                    </p>


                    <a
                        href="add-payment-method.php"
                        class="add-payment-btn"
                    >
                        Add Your First Payment Method
                    </a>

                </div>


            <?php endif; ?>

        </div>

    </div>

</main>


</body>

</html>