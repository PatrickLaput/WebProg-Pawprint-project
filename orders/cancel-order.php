<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit();
}

$user_id = (int) $_SESSION["user_id"];
$order_id = isset($_POST["order_id"])
    ? (int) $_POST["order_id"]
    : 0;

if ($order_id <= 0) {
    header("Location: orders.php?error=invalid_order");
    exit();
}

$stmt = $conn->prepare("
    SELECT status
    FROM orders
    WHERE order_id = ?
      AND user_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: orders.php?error=order_not_found");
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

if (
    $order["status"] === "Completed" ||
    $order["status"] === "Delivered" ||
    $order["status"] === "Cancelled"
) {
    header("Location: orders.php?error=cannot_cancel");
    exit();
}

$update_stmt = $conn->prepare("
    UPDATE orders
    SET status = 'Cancelled'
    WHERE order_id = ?
      AND user_id = ?
");

$update_stmt->bind_param("ii", $order_id, $user_id);
$update_stmt->execute();
$update_stmt->close();

header("Location: orders.php?cancelled=success");
exit();