<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../newsletter-subscribe.php";

$user_id = (int) $_SESSION["user_id"];

$product_id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($product_id <= 0) {
    header("Location: ../shop/shop.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Get product information
|--------------------------------------------------------------------------
*/

$product_stmt = $conn->prepare("
    SELECT
        prod_id,
        name,
        description,
        category,
        pet_type,
        price,
        rating,
        review_count,
        stock,
        image
    FROM products
    WHERE prod_id = ?
    LIMIT 1
");

$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();

$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    $product_stmt->close();
    header("Location: ../shop/shop.php");
    exit();
}

$product = $product_result->fetch_assoc();
$product_stmt->close();

/*
|--------------------------------------------------------------------------
| Get the current user's review
|--------------------------------------------------------------------------
*/

$my_review_stmt = $conn->prepare("
    SELECT
        review_id,
        rating,
        comment
    FROM product_reviews
    WHERE product_id = ?
      AND user_id = ?
    LIMIT 1
");

$my_review_stmt->bind_param("ii", $product_id, $user_id);
$my_review_stmt->execute();

$my_review_result = $my_review_stmt->get_result();
$my_review = $my_review_result->fetch_assoc();

$my_review_stmt->close();

/*
|--------------------------------------------------------------------------
| Get all reviews for this product
|--------------------------------------------------------------------------
*/

$reviews_stmt = $conn->prepare("
    SELECT
        r.rating,
        r.comment,
        r.created_at,
        r.updated_at,
        u.first_name,
        u.last_name
    FROM product_reviews r
    INNER JOIN users u
        ON r.user_id = u.userID
    WHERE r.product_id = ?
    ORDER BY r.updated_at DESC
");

$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();

$reviews_result = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>
        <?php echo htmlspecialchars($product["name"]); ?> | Pawprint
    </title>

    <link rel="stylesheet" href="product-details.css">

</head>

<body>

<script src="product-details.js"></script>

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

<main class="product-details-page">

    <a href="shop.php" class="back-shop-btn">
        ← Back to Shop
    </a>

    <section class="product-details-card">

        <?php
        $product_image_url = "../" . ltrim($product["image"], "/");
        ?>

        <div class="product-details-image">
            <?php if (!empty($product["image"])): ?>
                <img
                    src="<?php echo htmlspecialchars($product_image_url); ?>"
                    alt="<?php echo htmlspecialchars($product["name"]); ?>"
                >
            <?php else: ?>
                <div class="product-placeholder-large">🐾</div>
            <?php endif; ?>
        </div>

        <div class="product-details-info">

            <div class="product-category">
                <?php echo htmlspecialchars($product["category"]); ?>
            </div>

            <h1>
                <?php echo htmlspecialchars($product["name"]); ?>
            </h1>

            <div class="product-price">
                ₱<?php echo number_format($product["price"], 2); ?>
            </div>

            <div class="product-rating">
                ⭐ <?php echo number_format((float) $product["rating"], 1); ?>
                / 5
                ·
                <?php echo (int) $product["review_count"]; ?> reviews
            </div>

            <p class="product-description">
                <?php echo nl2br(htmlspecialchars($product["description"])); ?>
            </p>

            <div class="product-info-row">
                <strong>Category:</strong>
                <?php echo htmlspecialchars($product["category"]); ?>
            </div>

            <div class="product-info-row">
                <strong>Pet Type:</strong>
                <?php echo htmlspecialchars($product["pet_type"]); ?>
            </div>

            <div class="product-info-row">
                <strong>Available Stock:</strong>
                <?php echo (int) $product["stock"]; ?>
            </div>

            <form action="add-to-cart.php" method="POST" class="add-to-cart-form">
                <input
                    type="hidden"
                    name="product_id"
                    value="<?php echo (int) $product['prod_id']; ?>"
                >

                <input
                    type="number"
                    name="quantity"
                    value="1"
                    min="1"
                    max="<?php echo (int) $product['stock']; ?>"
                    class="quantity-input"
                >

                <button type="submit" class="add-cart">
                    Add to Cart
                </button>
            </form>

            <p id="cartMessage"></p>

        </div>
    </section>

    <section class="reviews-section">

        <h2>Rate and Review This Product</h2>

        <form
            method="POST"
            action="save-review.php"
            class="review-form"
        >
            <input
                type="hidden"
                name="product_id"
                value="<?php echo (int) $product["prod_id"]; ?>"
            >

            <div class="rating-section">
                <label class="rating-title">Your Rating</label>

                <div class="star-rating">
                    <input type="radio" id="star5" name="rating" value="5" required>
                    <label for="star5" title="5 stars">★</label>

                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4" title="4 stars">★</label>

                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3" title="3 stars">★</label>

                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2" title="2 stars">★</label>

                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1" title="1 star">★</label>
                </div>
            </div>

            <label for="comment">Your Comment</label>

            <textarea
                userID="comment"
                name="comment"
                placeholder="Write your review..."
                required
            ><?php
                echo $my_review
                    ? htmlspecialchars($my_review["comment"])
                    : "";
            ?></textarea>

            <button type="submit" class="review-submit-btn">
                <?php echo $my_review ? "Update Review" : "Submit Review"; ?>
            </button>
        </form>

        <h2>Customer Reviews</h2>

        <?php if ($reviews_result->num_rows === 0): ?>

            <p class="no-reviews">
                No reviews yet. Be the first to review this product.
            </p>

        <?php else: ?>

            <?php while ($review = $reviews_result->fetch_assoc()): ?>

                <div class="review-item">

                    <div class="review-user">
                        <?php
                        echo htmlspecialchars(
                            $review["first_name"] . " " . $review["last_name"]
                        );
                        ?>
                    </div>

                    <div class="review-stars">
                        <?php echo str_repeat("★", (int) $review["rating"]); ?>
                        <?php echo str_repeat("☆", 5 - (int) $review["rating"]); ?>
                    </div>

                    <div class="review-comment">
                        <?php echo nl2br(htmlspecialchars($review["comment"])); ?>
                    </div>

                    <div class="review-date">
                        Updated:
                        <?php echo date("F j, Y", strtotime($review["updated_at"])); ?>
                    </div>

                </div>

            <?php endwhile; ?>

        <?php endif; ?>

    </section>

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