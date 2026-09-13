<?php
 
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = (int)$_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| Check payment method ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET["userID"]) || !is_numeric($_GET["userID"])) {
    header("Location: payment-method.php");
    exit();
}

$payment_id = (int)$_GET["userID"];


/*
|--------------------------------------------------------------------------
| Check that the payment method belongs to this user
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT userID
    FROM payment_methods
    WHERE userID = ?
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $payment_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: payment-method.php?default=notfound");
    exit();
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Remove default status from all payment methods
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE payment_methods
    SET is_default = 0
    WHERE user_id = ?
");

$stmt->bind_param(
    "i",
    $user_id
);

if (!$stmt->execute()) {

    $error = $conn->error;

    $stmt->close();

    die("Unable to update payment methods: " . $error);
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Set selected payment method as default
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    UPDATE payment_methods
    SET is_default = 1
    WHERE userID = ?
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $payment_id,
    $user_id
);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: payment-method.php?default=success");
    exit();

} else {

    $error = $conn->error;

    $stmt->close();

    die("Unable to set default payment method: " . $error);
}

?>