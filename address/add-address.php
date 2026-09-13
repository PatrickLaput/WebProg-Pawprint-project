<?php
 

require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = $_SESSION["user_id"];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address_line = trim($_POST["address_line"] ?? "");
    $barangay = trim($_POST["barangay"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $province = trim($_POST["province"] ?? "");
    $postal_code = trim($_POST["postal_code"] ?? "");

    $is_default = isset($_POST["is_default"]) ? 1 : 0;


    // Validate required fields
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
         * If this address is being set as default,
         * remove the default status from the user's
         * existing addresses.
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
             * If the user doesn't have any addresses yet,
             * automatically make this one the default.
             */
            $check_stmt = $conn->prepare("
                SELECT COUNT(*) AS total
                FROM addresses
                WHERE user_id = ?
            ");

            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();

            $check_result = $check_stmt->get_result();
            $address_count = $check_result->fetch_assoc()["total"];

            $check_stmt->close();

            if ($address_count == 0) {
                $is_default = 1;
            }
        }


        // Insert the new address
        $stmt = $conn->prepare("
            INSERT INTO addresses
            (
                user_id,
                full_name,
                phone,
                address_line,
                barangay,
                city,
                province,
                postal_code,
                is_default
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssssi",
            $user_id,
            $full_name,
            $phone,
            $address_line,
            $barangay,
            $city,
            $province,
            $postal_code,
            $is_default
        );


        if ($stmt->execute()) {

            $stmt->close();

            header("Location: addresses.php?added=success");
            exit();

        } else {

            $error = "Something went wrong. Please try again.";

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pawprint</title>

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


            <div class="add-address-header">

                <a
                    href="addresses.php"
                    class="back-link"
                >
                    ← Back to Addresses
                </a>

                <h1>Add New Address</h1>

                <p>
                    Add a delivery address to your Pawprint account.
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
                    action="add-address.php"
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
                            value="<?php echo htmlspecialchars($_POST["full_name"] ?? ""); ?>"
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
                            value="<?php echo htmlspecialchars($_POST["phone"] ?? ""); ?>"
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
                            value="<?php echo htmlspecialchars($_POST["address_line"] ?? ""); ?>"
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
                            value="<?php echo htmlspecialchars($_POST["barangay"] ?? ""); ?>"
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
                                value="<?php echo htmlspecialchars($_POST["city"] ?? ""); ?>"
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
                                value="<?php echo htmlspecialchars($_POST["province"] ?? ""); ?>"
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
                            value="<?php echo htmlspecialchars($_POST["postal_code"] ?? ""); ?>"
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
                                <?php echo isset($_POST["is_default"]) ? "checked" : ""; ?>
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
                            Save Address
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