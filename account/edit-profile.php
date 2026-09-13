<?php

 

require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = $_SESSION["user_id"];

$error = "";

/* ========================================
   GET CURRENT USER
======================================== */

$stmt = $conn->prepare(
    "SELECT first_name, last_name, email, birthdate, profile_picture
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
   UPDATE PROFILE
======================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $birthdate = $_POST["birthdate"];

    $profile_picture = $user["profile_picture"];


    /* ========================================
       CHECK EMAIL
    ======================================== */

    $check = $conn->prepare(
        "SELECT userID FROM users
         WHERE email = ? AND userID != ?"
    );

    $check->bind_param("si", $email, $user_id);
    $check->execute();

    $check_result = $check->get_result();


    if ($check_result->num_rows > 0) {

        $error = "That email address is already being used.";

    } else {


        /* ========================================
           PROFILE PICTURE UPLOAD
        ======================================== */

        if (
            isset($_FILES["profile_picture"]) &&
            $_FILES["profile_picture"]["error"] === UPLOAD_ERR_OK
        ) {

            $file = $_FILES["profile_picture"];

            $allowed_types = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];

            $file_type = mime_content_type($file["tmp_name"]);


            /* Check file type */

            if (!in_array($file_type, $allowed_types)) {

                $error = "Please upload a JPG, PNG, or WEBP image.";

            }

            /* Check file size */

            elseif ($file["size"] > 5 * 1024 * 1024) {

                $error = "The profile picture must be less than 5MB.";

            }

            else {

                /* Create unique filename */

                $extension = strtolower(
                    pathinfo(
                        $file["name"],
                        PATHINFO_EXTENSION
                    )
                );

                $new_filename =
                    "user_" .
                    $user_id .
                    "_" .
                    time() .
                    "." .
                    $extension;


                $upload_directory =
                    "../uploads/profiles/";

                $upload_path =
                    $upload_directory .
                    $new_filename;


                /* Move uploaded file */

                if (
                    move_uploaded_file(
                        $file["tmp_name"],
                        $upload_path
                    )
                ) {

                    $profile_picture = $new_filename;

                } else {

                    $error = "Unable to upload the profile picture.";

                }

            }
        }


        /* ========================================
           UPDATE DATABASE
        ======================================== */

        if ($error === "") {

            $stmt = $conn->prepare(
                "UPDATE users
                 SET first_name = ?,
                     last_name = ?,
                     email = ?,
                     birthdate = ?,
                     profile_picture = ?
                 WHERE userID = ?"
            );

            $stmt->bind_param(
                "sssssi",
                $first_name,
                $last_name,
                $email,
                $birthdate,
                $profile_picture,
                $user_id
            );


            if ($stmt->execute()) {

                /* Update session */

                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["email"] = $email;


                header("Location: account.php?updated=success");
                exit();

            } else {

                $error = "Unable to update your profile.";

            }

            $stmt->close();
        }
    }

    $check->close();
}


/* ========================================
   PROFILE IMAGE PATH
======================================== */

if (
    !empty($user["profile_picture"]) &&
    file_exists("uploads/profiles/" . $user["profile_picture"])
) {

    $profile_image =
        "uploads/profiles/" .
        htmlspecialchars($user["profile_picture"]);

} else {

    $profile_image =
        "../images/profile.png";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pawprint</title>

    <link
        rel="stylesheet"
        href="edit-profile.css"
    >

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

<section class="edit-profile-page">

    <div class="edit-profile-container">


        <!-- ========================================
             BACK BUTTON
        ======================================== -->

        <a
            href="account.php"
            class="back-account"
        >
            ← Back to My Account
        </a>


        <!-- ========================================
             HEADING
        ======================================== -->

        <div class="edit-heading">

            <h1>Edit Profile</h1>

            <p>
                Update your personal information below.
            </p>

        </div>


        <!-- ========================================
             EDIT CARD
        ======================================== -->

        <div class="edit-card">


            <!-- Profile Picture -->

            <div class="edit-profile-picture">

                <img
                    src="<?php echo $profile_image; ?>"
                    alt="Profile Picture"
                    id="profile-preview"
                >

            </div>


            <h2>
                Personal Information
            </h2>


            <?php if ($error !== ""): ?>

                <div class="form-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <form
                action="edit-profile.php"
                method="POST"
                enctype="multipart/form-data"
                class="edit-form"
            >


                <!-- ========================================
                     PROFILE PICTURE
                ======================================== -->

                <div class="edit-field profile-picture-field">

                    <label for="profile_picture">
                        Profile Picture
                    </label>

                    <input
                        type="file"
                        id="profile_picture"
                        name="profile_picture"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <small>
                        JPG, PNG, or WEBP. Maximum 5MB.
                    </small>

                </div>


                <!-- ========================================
                     FIRST NAME
                ======================================== -->

                <div class="edit-field">

                    <label for="first_name">
                        First Name
                    </label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="<?php echo htmlspecialchars($user["first_name"]); ?>"
                        required
                    >

                </div>


                <!-- ========================================
                     LAST NAME
                ======================================== -->

                <div class="edit-field">

                    <label for="last_name">
                        Last Name
                    </label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="<?php echo htmlspecialchars($user["last_name"]); ?>"
                        required
                    >

                </div>


                <!-- ========================================
                     EMAIL
                ======================================== -->

                <div class="edit-field">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($user["email"]); ?>"
                        required
                    >

                </div>


                <!-- ========================================
                     BIRTHDATE
                ======================================== -->

                <div class="edit-field">

                    <label for="birthdate">
                        Birthdate
                    </label>

                    <input
                        type="date"
                        id="birthdate"
                        name="birthdate"
                        value="<?php echo htmlspecialchars($user["birthdate"]); ?>"
                        required
                    >

                </div>


                <!-- ========================================
                     BUTTONS
                ======================================== -->

                <div class="edit-buttons">

                    <a
                        href="account.php"
                        class="cancel-button"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="save-button"
                    >
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

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

<script src="edit-profile.js" ></script>

</body>
</html>