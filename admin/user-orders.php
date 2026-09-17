<?php
require_once "admin-auth.php";
require_once "../db.php";

$user_id = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : 0;

if ($user_id <= 0) {
    header("Location: manage-users.php");
    exit;
}

/* Update order status */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {
    $order_id = (int) $_POST["order_id"];
    $new_status = $_POST["status"];

    $allowed_statuses = [
        "Pending",
        "Processing",
        "Shipped",
        "Delivered",
        "Cancelled"
    ];

    if (in_array($new_status, $allowed_statuses, true)) {
        $update_stmt = $conn->prepare("
            UPDATE orders
            SET status = ?
            WHERE order_id = ?
            AND user_id = ?
        ");

        $update_stmt->bind_param(
            "sii",
            $new_status,
            $order_id,
            $user_id
        );

        $update_stmt->execute();
        $update_stmt->close();
    }

    header("Location: user-orders.php?user_id=" . $user_id);
    exit;
}

/* Get user information */
$user_stmt = $conn->prepare("
    SELECT userId, first_name, last_name, email
    FROM users
    WHERE userId = ?
");

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

$user_stmt->close();

if (!$user) {
    header("Location: manage-users.php");
    exit;
}

/* Get user's orders */
$order_stmt = $conn->prepare("
    SELECT
        order_id,
        total_amount,
        status,
        shipping_full_name,
        shipping_address,
        shipping_barangay,
        shipping_city,
        shipping_province,
        shipping_postal_code,
        payment_type,
        created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();

$orders = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders | Pawprint</title>
    <link rel="stylesheet" href="manage-users.css">
</head>
<body>

<header class="admin-header">
    <h1>User Orders</h1>
    <a href="manage-users.php">Back to Users</a>
</header>

<main class="admin-container">
    <section class="orders-card">

        <h2>
            <?= htmlspecialchars($user["first_name"] . " " . $user["last_name"]) ?>
        </h2>

        <p class="user-email">
            Email: <?= htmlspecialchars($user["email"]) ?>
        </p>

        <?php if ($orders->num_rows === 0): ?>

            <p class="no-orders-message">
                This user has no orders yet.
            </p>

        <?php else: ?>

            <div class="orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Shipping Information</th>
                            <th>Payment Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($order["order_id"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($order["created_at"]) ?>
                                </td>

                                <td>
                                    ₱<?= number_format((float) $order["total_amount"], 2) ?>
                                </td>

                                <td>
                                    <span class="status status-<?= strtolower(htmlspecialchars($order["status"])) ?>">
                                        <?= htmlspecialchars($order["status"]) ?>
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($order["shipping_full_name"]) ?>
                                    </strong><br>

                                    <?= htmlspecialchars($order["shipping_address"]) ?><br>
                                    <?= htmlspecialchars($order["shipping_barangay"]) ?>,
                                    <?= htmlspecialchars($order["shipping_city"]) ?>,
                                    <?= htmlspecialchars($order["shipping_province"]) ?><br>
                                    <?= htmlspecialchars($order["shipping_postal_code"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($order["payment_type"]) ?>
                                </td>

                                <td>
                                    <form method="POST" class="status-form">
                                        <input
                                            type="hidden"
                                            name="order_id"
                                            value="<?= htmlspecialchars($order["order_id"]) ?>"
                                        >

                                        <select name="status" class="status-select">
                                            <option value="Pending"
                                                <?= $order["status"] === "Pending" ? "selected" : "" ?>>
                                                Pending
                                            </option>

                                            <option value="Processing"
                                                <?= $order["status"] === "Processing" ? "selected" : "" ?>>
                                                Processing
                                            </option>

                                            <option value="Shipped"
                                                <?= $order["status"] === "Shipped" ? "selected" : "" ?>>
                                                Shipped
                                            </option>

                                            <option value="Delivered"
                                                <?= $order["status"] === "Delivered" ? "selected" : "" ?>>
                                                Delivered
                                            </option>

                                            <option value="Cancelled"
                                                <?= $order["status"] === "Cancelled" ? "selected" : "" ?>>
                                                Cancelled
                                            </option>
                                        </select>

                                        <button
                                            type="submit"
                                            name="update_status"
                                            class="update-status-button"
                                        >
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>
</main>

</body>
</html>