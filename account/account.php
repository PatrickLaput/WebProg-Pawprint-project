<?php

require_once __DIR__ . "/../newsletter-subscribe.php";
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

/* ========================================
   GET CURRENT USER
======================================== */

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT userID, first_name, last_name, email, birthdate, created_at, profile_picture
     FROM users
     WHERE userID = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    session_destroy();
    header("Location: ../signup/signup.php");
    exit();
}

$user = $result->fetch_assoc();

$stmt->close();


/* ========================================
   FORMAT USER INFORMATION
======================================== */

$full_name = htmlspecialchars(
    $user["first_name"] . " " . $user["last_name"]
);

$first_name = htmlspecialchars($user["first_name"]);
$last_name = htmlspecialchars($user["last_name"]);
$email = htmlspecialchars($user["email"]);

$birthdate = !empty($user["birthdate"])
    ? date("m/d/Y", strtotime($user["birthdate"]))
    : "MM/DD/YYYY";

$joined_date = !empty($user["created_at"])
    ? date("F Y", strtotime($user["created_at"]))
    : "Recently";

$profile_folder = __DIR__ . "/../uploads/profiles/";

if (
    !empty($user["profile_picture"]) &&
    file_exists($profile_folder . $user["profile_picture"])
) {
    $profile_image =
        "../uploads/profiles/" .
        rawurlencode($user["profile_picture"]);
} else {
    $profile_image = "../images/profile.png";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    
    <title>Pawprint</title>

    <link rel="stylesheet" href="account.css">

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
                <a href="account.php">
                    <img src="../images/acc.png" alt="Account">
                </a>
            </div>
        </div>
    </header>

    <section class="account-page">

        <div class="account-container">

            <aside class="account-sidebar">

                <!-- Profile -->
                <div class="sidebar-profile">

                    <div class="sidebar-profile-image">
                        <img
                            src="<?php echo $profile_image; ?>"
                            alt="Profile"
                        >
                    </div>

                    <h3>
                        <?php echo $full_name; ?>
                    </h3>

                    <p>
                        <?php echo $email; ?>
                    </p>

                </div>


                <!-- Navigation -->
                <nav class="account-navigation">

                    <a href="account.php" class="account-nav-item active">
                        <span>Account Overview</span>
                    </a>


                    <a href="../orders/orders.php" class="account-nav-item">
                        <span>My Orders</span>
                    </a>

                    <a href="../address/addresses.php" class="account-nav-item">
                        <span>Addresses</span>
                    </a>

                    <a href="../payment method/payment-method.php" class="account-nav-item">
                        <span>Payment Methods</span>
                    </a>

                    <a href="logout.php" class="account-nav-item">
                        <span class="nav-icon">↪</span>
                        <span>Log Out</span>
                    </a>

                    </nav>

            </aside>

            <main class="account-main">


                <!-- Decorative Paw -->
                <img
                    src="../images/pawprint.png"
                    alt=""
                    class="account-paw"
                >


                <!-- Page Heading -->
                <div class="account-heading">

                    <h1>My Account</h1>

                    <p>
                        Manage your profile, view your orders, and keep<br>
                        track of your pet's happiness.
                    </p>

                </div>



                <!-- ========================================
                    ACCOUNT CARD
                ======================================== -->

                <div class="account-card">


                    <!-- User Header -->
                    <div class="account-user-header">


                        <div class="account-user-left">

                            <div class="account-profile-image">

                                <img
                                    src="<?php echo $profile_image; ?>"
                                    alt="Profile"
                                >

                            </div>


                            <div class="account-user-info">

                                <h2>
                                    <?php echo $full_name; ?>
                                </h2>

                                <p>
                                    <?php echo $email; ?>
                                </p>

                                <p>
                                    Joined <?php echo $joined_date; ?>
                                </p>

                            </div>

                        </div>


                        <!-- Edit Profile -->
                        <a
                            href="edit-profile.php"
                            class="edit-profile-button"
                        >

                            <span>↗</span>
                            Edit Profile

                        </a>

                    </div>



                    <!-- Divider -->
                    <div class="account-divider"></div>



                    <!-- ========================================
                        PERSONAL INFORMATION
                    ======================================== -->

                    <div class="personal-information">

                        <h3>Personal Information</h3>


                        <div class="information-grid">


                            <!-- First Name -->
                            <div class="information-item">

                                <span class="information-label">
                                    First Name
                                </span>

                                <span class="information-value">
                                    <?php echo $first_name; ?>
                                </span>

                            </div>


                            <!-- Email -->
                            <div class="information-item">

                                <span class="information-label">
                                    Email Address
                                </span>

                                <span class="information-value">
                                    <?php echo $email; ?>
                                </span>

                            </div>


                            <!-- Last Name -->
                            <div class="information-item">

                                <span class="information-label">
                                    Last Name
                                </span>

                                <span class="information-value">
                                    <?php echo $last_name; ?>
                                </span>

                            </div>


                            <!-- Birthdate -->
                            <div class="information-item">

                                <span class="information-label">
                                    Birthdate
                                </span>

                                <span class="information-value">
                                    <?php echo $birthdate; ?>
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </main>
        </div>

    </section>

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
                <a href="account.php">My Account</a>
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