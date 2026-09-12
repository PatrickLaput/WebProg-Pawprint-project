<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit();
}

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
| Find the payment method
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        userID,
        is_default
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


/*
|--------------------------------------------------------------------------
| Payment method does not exist
|--------------------------------------------------------------------------
*/

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: payment-method.php?deleted=notfound");
    exit();
}


$payment = $result->fetch_assoc();

$was_default = (int)$payment["is_default"];

$stmt->close();


/*
|--------------------------------------------------------------------------
| Delete payment method
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    DELETE FROM payment_methods
    WHERE userID = ?
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $payment_id,
    $user_id
);


if (!$stmt->execute()) {

    $error = $conn->error;

    $stmt->close();

    die("Unable to delete payment method: " . $error);
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| If the deleted payment method was the default,
| make another payment method the default.
|--------------------------------------------------------------------------
*/

if ($was_default === 1) {

    $stmt = $conn->prepare("
        SELECT userID
        FROM payment_methods
        WHERE user_id = ?
        ORDER BY userID DESC
        LIMIT 1
    ");

    $stmt->bind_param(
        "i",
        $user_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0) {

        $new_default = $result->fetch_assoc();

        $new_default_id = (int)$new_default["userID"];

        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | Set the newest remaining payment method as default
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
            $new_default_id,
            $user_id
        );

        $stmt->execute();

        $stmt->close();

    } else {

        $stmt->close();
    }
}


/*
|--------------------------------------------------------------------------
| Return to payment methods
|--------------------------------------------------------------------------
*/

header("Location: payment-method.php?deleted=success");
exit();

?>