<?php
require_once "admin-auth.php";
require_once "../db.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$user_id = (int) ($_GET["user_id"] ?? 0);

if ($user_id <= 0) {
    header("Location: manage-users.php");
    exit;
}

/* Get user information */
$user_stmt = $conn->prepare("
    SELECT userId, first_name, last_name, email
    FROM users
    WHERE userId = ?
    LIMIT 1
");

$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    header("Location: manage-users.php");
    exit;
}

/* Get the selected user's orders */
$order_stmt = $conn->prepare("
    SELECT
        order_id,
        user_id,
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

$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();

$orders = $order_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="manage-users.css">

    <title>User Orders | Pawprint</title>
</head>
<body>

<header class="admin-header">
    <h1>User Orders</h1>

    <a href="manage-users.php">Back to Users</a>
</header>

<main class="admin-container">

    <section class="admin-card">
        <h2>
            <?= htmlspecialchars(
                $user["first_name"] . " " . $user["last_name"]
            ) ?>
        </h2>

        <p>
            Email:
            <?= htmlspecialchars($user["email"]) ?>
        </p>
    </section>

    <section class="admin-card">
        <h2>Order History</h2>
        
        <?php if ($orders->num_rows > 0): ?>
            <div class="user-table-wrapper">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Customer Name</th>
                            <th>Shipping Address</th>
                            <th>Payment Type</th>
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
                                    <?= htmlspecialchars($order["status"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($order["shipping_full_name"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $order["shipping_address"] . ", " .
                                        $order["shipping_barangay"] . ", " .
                                        $order["shipping_city"] . ", " .
                                        $order["shipping_province"] . ", " .
                                        $order["shipping_postal_code"]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($order["payment_type"]) ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <p>This user has no orders yet.</p>
        <?php endif; ?>
        
    </section>

</main>

</body>
</html>