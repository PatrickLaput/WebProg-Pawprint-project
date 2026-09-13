<?php
 

require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = (int)$_SESSION["user_id"];

$error = "";

$payment_type = "";
$account_name = "";
$account_number = "";
$is_default = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $payment_type = trim($_POST["payment_type"] ?? "");
    $account_name = trim($_POST["account_name"] ?? "");
    $account_number = trim($_POST["account_number"] ?? "");
    $is_default = isset($_POST["is_default"]) ? 1 : 0;

    $allowed_types = [
        "Cash on Delivery",
        "GCash",
        "Maya",
        "Credit / Debit Card"
    ];

    // Validate payment type
    if (!in_array($payment_type, $allowed_types, true)) {
        $error = "Please select a valid payment method.";
    }

    // GCash and Maya validation
    if (
        empty($error) &&
        ($payment_type === "GCash" || $payment_type === "Maya")
    ) {
        if ($account_name === "" || $account_number === "") {
            $error = "Please enter your account name and account number.";
        }

        if (
            empty($error) &&
            (!ctype_digit($account_number) || strlen($account_number) !== 11)
        ) {
            $error = "Please enter an 11-digit account number.";
        }
    }

    // Credit / Debit Card validation
    if (
        empty($error) &&
        $payment_type === "Credit / Debit Card"
    ) {
        if ($account_name === "" || $account_number === "") {
            $error = "Please enter the cardholder name and card number.";
        }

        if (
            empty($error) &&
            (!ctype_digit($account_number) || strlen($account_number) !== 16)
        ) {
            $error = "Please enter a valid 16-digit card number.";
        }
    }

    // COD does not need account details
    if (
        empty($error) &&
        $payment_type === "Cash on Delivery"
    ) {
        $account_name = "";
        $account_number = "";
    }

    if (empty($error)) {

        // Check if the user already has payment methods
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS total
            FROM payment_methods
            WHERE user_id = ?
        ");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $total_methods = (int)$row["total"];

        $stmt->close();

        // First payment method automatically becomes default
        if ($total_methods === 0) {
            $is_default = 1;
        }

        // Remove default from other payment methods
        if ($is_default === 1) {

            $stmt = $conn->prepare("
                UPDATE payment_methods
                SET is_default = 0
                WHERE user_id = ?
            ");

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }

        // Insert payment method
        $stmt = $conn->prepare("
            INSERT INTO payment_methods
            (
                user_id,
                payment_type,
                account_name,
                account_number,
                is_default
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssi",
            $user_id,
            $payment_type,
            $account_name,
            $account_number,
            $is_default
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: payment-method.php?added=success");
            exit();

        } else {

            $error = "Unable to add payment method: " . $conn->error;

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pawprint</title>

    <link rel="stylesheet" href="add-payment-method.css">


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

    <main class="add-payment-page">

        <div class="add-payment-container">

            <div class="add-payment-header">

                <h1>Add Payment Method</h1>

                <p>
                    Add a payment method to make checkout faster and easier.
                </p>

            </div>


            <?php if ($error !== ""): ?>

                <div class="payment-error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <form
                class="payment-form"
                method="POST"
            >

                <div class="form-group">

                    <label for="payment_type">
                        Payment Method
                    </label>

                    <select
                        name="payment_type"
                        id="payment_type"
                        required
                    >

                        <option value="">
                            Select payment method
                        </option>

                        <option
                            value="Cash on Delivery"
                            <?php echo $payment_type === "Cash on Delivery" ? "selected" : ""; ?>
                        >
                            Cash on Delivery
                        </option>

                        <option
                            value="GCash"
                            <?php echo $payment_type === "GCash" ? "selected" : ""; ?>
                        >
                            GCash
                        </option>

                        <option
                            value="Maya"
                            <?php echo $payment_type === "Maya" ? "selected" : ""; ?>
                        >
                            Maya
                        </option>

                        <option
                            value="Credit / Debit Card"
                            <?php echo $payment_type === "Credit / Debit Card" ? "selected" : ""; ?>
                        >
                            Credit / Debit Card
                        </option>

                    </select>

                </div>


                <div
                    class="account-fields"
                    id="account-fields"
                >

                    <div class="form-group">

                        <label
                            for="account_name"
                            id="account-name-label"
                        >
                            Account Name
                        </label>

                        <input
                            type="text"
                            name="account_name"
                            id="account_name"
                            value="<?php echo htmlspecialchars($account_name); ?>"
                            placeholder="Enter account name"
                        >

                    </div>


                    <div class="form-group">

                        <label
                            for="account_number"
                            id="account-number-label"
                        >
                            Account Number
                        </label>

                        <input
                            type="text"
                            name="account_number"
                            id="account_number"
                            value="<?php echo htmlspecialchars($account_number); ?>"
                            placeholder="Enter account number"
                        >

                    </div>

                </div>


                <label class="default-option">

                    <input
                        type="checkbox"
                        name="is_default"
                        value="1"
                        <?php echo $is_default ? "checked" : ""; ?>
                    >

                    <span>
                        Set as my default payment method
                    </span>

                </label>


                <div class="form-actions">

                    <a
                        href="payment-method.php"
                        class="cancel-btn"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="save-btn"
                    >
                        Add Payment Method
                    </button>

                </div>

            </form>

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


    <script src="add-payment-method.js"></script>

</body>

</html>