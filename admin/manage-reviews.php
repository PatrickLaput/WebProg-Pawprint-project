<?php
require_once "admin-auth.php";
require_once "../db.php";

$message = "";
$error = "";

/*
|--------------------------------------------------------------------------
| Delete review
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_review"])) {
    $reviewId = intval($_POST["review_id"]);

    if ($reviewId > 0) {
        $sql = "DELETE FROM product_reviews WHERE review_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $reviewId);

            if ($stmt->execute()) {
                $message = "Review deleted successfully.";
            } else {
                $error = "Unable to delete the review.";
            }

            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Invalid review ID.";
    }
}

/*
|--------------------------------------------------------------------------
| Get all reviews
|--------------------------------------------------------------------------
*/
$reviews = [];

$sql = "
    SELECT
        r.review_id,
        r.rating,
        r.comment,
        r.created_at,
        r.updated_at,
        p.name,
        u.first_name,
        u.last_name,
        u.email
    FROM product_reviews r
    LEFT JOIN products p
        ON r.product_id = p.prod_id
    LEFT JOIN users u
        ON r.user_id = u.userID
    ORDER BY r.review_id DESC
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
} else {
    $error = "Unable to load reviews: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <link rel="stylesheet" type="text/css" href="manage-reviews.css">

    <title>Pawprint Admin</title>

</head>

<body>

<header>
    <h1>Pawprint Admin - Manage Reviews</h1>
    <a href="admin-dashboard.php">Back to Dashboard</a>
</header>

<div class="container">

    <?php if ($message !== ""): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <section class="review-card">
        <h2>Customer Reviews</h2>

        <div class="table-wrapper">
            <?php if (count($reviews) > 0): ?>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td>
                                    <?= (int) $review["review_id"] ?>
                                </td>

                                <td>
                                    <div class="customer-name">
                                        <?php
                                        $fullName = trim(
                                            ($review["first_name"] ?? "") . " " .
                                            ($review["last_name"] ?? "")
                                        );

                                        echo htmlspecialchars(
                                            $fullName !== ""
                                                ? $fullName
                                                : "Unknown customer"
                                        );
                                        ?>
                                    </div>

                                    <?php if (!empty($review["email"])): ?>
                                        <div class="customer-email">
                                            <?= htmlspecialchars($review["email"]) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="product-name">
                                        <?= htmlspecialchars(
                                            $review["prod_name"] ?? "Unknown product"
                                        ) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="rating">
                                        <?php
                                        $rating = (int) $review["rating"];

                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $rating ? "★" : "☆";
                                        }
                                        ?>
                                    </div>

                                    <small>
                                        <?= $rating ?>/5
                                    </small>
                                </td>

                                <td>
                                    <div class="comment">
                                        <?= nl2br(
                                            htmlspecialchars(
                                                $review["comment"] ?? ""
                                            )
                                        ) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="date">
                                        <?= htmlspecialchars(
                                            $review["updated_at"]
                                                ?? $review["created_at"]
                                                ?? "No date"
                                        ) ?>
                                    </div>
                                </td>

                                <td>
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this review?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="review_id"
                                            value="<?= (int) $review["review_id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="delete_review"
                                            class="delete-button"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <div class="empty">
                    No customer reviews found.
                </div>
            <?php endif; ?>
        </div>
    </section>

</div>

</body>
</html>