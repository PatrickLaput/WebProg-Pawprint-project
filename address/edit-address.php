<?php

require_once __DIR__ . "/../newsletter-subscribe.php";
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = $_SESSION["user_id"];

$error = "";

// Get address ID from URL
$address_id = isset($_GET["userID"]) ? (int)$_GET["userID"] : 0;

if ($address_id <= 0) {
    header("Location: addresses.php");
    exit();
}


// Get the address
$stmt = $conn->prepare("
    SELECT
        userID,
        user_id,
        full_name,
        phone,
        address_line,
        barangay,
        city,
        province,
        postal_code,
        is_default
    FROM addresses
    WHERE userID = ? AND user_id = ?
");

$stmt->bind_param("ii", $address_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: addresses.php");
    exit();
}

$address = $result->fetch_assoc();

$stmt->close();


// Update address
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address_line = trim($_POST["address_line"] ?? "");
    $barangay = trim($_POST["barangay"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $province = trim($_POST["province"] ?? "");
    $postal_code = trim($_POST["postal_code"] ?? "");

    $is_default = isset($_POST["is_default"]) ? 1 : 0;


    // Validate fields
    if (
        empty($full_name) ||
        empty($phone) ||
        empty($address_line) ||
        empty($barangay) ||
        empty($city) ||
        empty($province) ||
        empty($postal_code)
    ) {

        $error = "Please fill in all fields.";

    } else {

        /*
         * If this address is being made default,
         * remove default status from the user's
         * other addresses.
         */
        if ($is_default == 1) {

            $reset_stmt = $conn->prepare("
                UPDATE addresses
                SET is_default = 0
                WHERE user_id = ?
            ");

            $reset_stmt->bind_param("i", $user_id);
            $reset_stmt->execute();

            $reset_stmt->close();

        } else {

            /*
             * Check whether another default address exists.
             */
            $check_stmt = $conn->prepare("
                SELECT userID
                FROM addresses
                WHERE user_id = ?
                AND is_default = 1
                AND userID != ?
                LIMIT 1
            ");

            $check_stmt->bind_param(
                "ii",
                $user_id,
                $address_id
            );

            $check_stmt->execute();

            $check_result = $check_stmt->get_result();

            /*
             * If there is no other default address,
             * keep this address as default.
             */
            if ($check_result->num_rows === 0) {
                $is_default = 1;
            }

            $check_stmt->close();
        }


        // Update the address
        $update_stmt = $conn->prepare("
            UPDATE addresses
            SET
                full_name = ?,
                phone = ?,
                address_line = ?,
                barangay = ?,
                city = ?,
                province = ?,
                postal_code = ?,
                is_default = ?
            WHERE userID = ? AND user_id = ?
        ");

        $update_stmt->bind_param(
            "sssssssiii",
            $full_name,
            $phone,
            $address_line,
            $barangay,
            $city,
            $province,
            $postal_code,
            $is_default,
            $address_id,
            $user_id
        );


        if ($update_stmt->execute()) {

            $update_stmt->close();

            header("Location: addresses.php?updated=success");
            exit();

        } else {

            $error = "Something went wrong. Please try again.";

            $update_stmt->close();
        }
    }


    /*
     * Keep the user's entered values on the form
     * if validation fails.
     */
    $address["full_name"] = $full_name;
    $address["phone"] = $phone;
    $address["address_line"] = $address_line;
    $address["barangay"] = $barangay;
    $address["city"] = $city;
    $address["province"] = $province;
    $address["postal_code"] = $postal_code;
    $address["is_default"] = $is_default;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>awprint</title>

    <link rel="stylesheet" href="add-address.css">

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

<main class="add-address-page">

    <div class="add-address-container">


        <!-- HEADER -->

        <div class="add-address-header">

            <a
                href="addresses.php"
                class="back-link"
            >
                ← Back to Addresses
            </a>


            <h1>Edit Address</h1>

            <p>
                Update your delivery address information.
            </p>

        </div>



        <!-- FORM CARD -->

        <div class="address-form-card">


            <?php if (!empty($error)): ?>

                <div class="form-error">

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>


            <form
                action="edit-address.php?userID=<?php echo (int)$address_id; ?>"
                method="POST"
                class="address-form"
            >


                <!-- FULL NAME -->

                <div class="form-group">

                    <label for="full_name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        placeholder="Enter full name"
                        value="<?php echo htmlspecialchars($address["full_name"]); ?>"
                        required
                    >

                </div>



                <!-- PHONE -->

                <div class="form-group">

                    <label for="phone">
                        Phone Number
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="Enter phone number"
                        value="<?php echo htmlspecialchars($address["phone"]); ?>"
                        required
                    >

                </div>



                <!-- ADDRESS -->

                <div class="form-group">

                    <label for="address_line">
                        House No. / Street / Building
                    </label>

                    <input
                        type="text"
                        id="address_line"
                        name="address_line"
                        placeholder="House number, street, building, etc."
                        value="<?php echo htmlspecialchars($address["address_line"]); ?>"
                        required
                    >

                </div>



                <!-- BARANGAY -->

                <div class="form-group">

                    <label for="barangay">
                        Barangay
                    </label>

                    <input
                        type="text"
                        id="barangay"
                        name="barangay"
                        placeholder="Enter barangay"
                        value="<?php echo htmlspecialchars($address["barangay"]); ?>"
                        required
                    >

                </div>



                <!-- CITY + PROVINCE -->

                <div class="form-row">

                    <div class="form-group">

                        <label for="city">
                            City / Municipality
                        </label>

                        <input
                            type="text"
                            id="city"
                            name="city"
                            placeholder="Enter city"
                            value="<?php echo htmlspecialchars($address["city"]); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="province">
                            Province
                        </label>

                        <input
                            type="text"
                            id="province"
                            name="province"
                            placeholder="Enter province"
                            value="<?php echo htmlspecialchars($address["province"]); ?>"
                            required
                        >

                    </div>

                </div>



                <!-- POSTAL CODE -->

                <div class="form-group">

                    <label for="postal_code">
                        Postal Code
                    </label>

                    <input
                        type="text"
                        id="postal_code"
                        name="postal_code"
                        placeholder="Enter postal code"
                        value="<?php echo htmlspecialchars($address["postal_code"]); ?>"
                        required
                    >

                </div>



                <!-- DEFAULT ADDRESS -->

                <div class="default-option">

                    <label class="checkbox-label">

                        <input
                            type="checkbox"
                            name="is_default"
                            value="1"
                            <?php
                            if ($address["is_default"] == 1) {
                                echo "checked";
                            }
                            ?>
                        >

                        <span>
                            Set as default address
                        </span>

                    </label>


                    <small>
                        Your default address will be used automatically during checkout.
                    </small>

                </div>



                <!-- BUTTONS -->

                <div class="form-buttons">

                    <a
                        href="addresses.php"
                        class="cancel-btn"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="save-btn"
                    >
                        Save Changes
                    </button>

                </div>


            </form>

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

</body>

</html>