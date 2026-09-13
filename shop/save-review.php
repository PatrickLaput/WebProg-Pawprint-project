<?php

require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../shop.php");
    exit();
}

$user_id = (int) $_SESSION["user_id"];

$product_id = isset($_POST["product_id"])
    ? (int) $_POST["product_id"]
    : 0;

$rating = isset($_POST["rating"])
    ? (int) $_POST["rating"]
    : 0;

$comment = trim($_POST["comment"] ?? "");

if (
    $product_id <= 0 ||
    $rating < 1 ||
    $rating > 5 ||
    $comment === ""
) {
    header("Location: product-details.php?id=" . $product_id . "&error=invalid");
    exit();
}

/*
|--------------------------------------------------------------------------
| Insert or update the user's review
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    INSERT INTO product_reviews
    (
        product_id,
        user_id,
        rating,
        comment
    )
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        rating = VALUES(rating),
        comment = VALUES(comment),
        updated_at = CURRENT_TIMESTAMP
");

$stmt->bind_param(
    "iiis",
    $product_id,
    $user_id,
    $rating,
    $comment
);

$stmt->execute();
$stmt->close();

/*
|--------------------------------------------------------------------------
| Recalculate the product's average rating and review count
|--------------------------------------------------------------------------
*/

$rating_stmt = $conn->prepare("
    SELECT
        AVG(rating) AS average_rating,
        COUNT(*) AS total_reviews
    FROM product_reviews
    WHERE product_id = ?
");

$rating_stmt->bind_param("i", $product_id);
$rating_stmt->execute();

$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();

$average_rating = (float) $rating_data["average_rating"];
$total_reviews = (int) $rating_data["total_reviews"];

$rating_stmt->close();

/*
|--------------------------------------------------------------------------
| Update the products table
|--------------------------------------------------------------------------
*/

$update_product_stmt = $conn->prepare("
    UPDATE products
    SET rating = ?,
        review_count = ?
    WHERE prod_id = ?
");

$update_product_stmt->bind_param(
    "dii",
    $average_rating,
    $total_reviews,
    $product_id
);

$update_product_stmt->execute();
$update_product_stmt->close();

header("Location: product-details.php?id=" . $product_id . "&review=success");
exit();