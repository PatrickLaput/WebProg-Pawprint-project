<?php

 

require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = (int)$_SESSION["user_id"];


if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {
    header("Location: cart.php");
    exit();
}

$cart_id = (int)$_GET["id"];


/*
|--------------------------------------------------------------------------
| Delete only this user's cart item
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    DELETE FROM cart_items
    WHERE id = ?
    AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $cart_id,
    $user_id
);

$stmt->execute();

$stmt->close();


header("Location: cart.php");
exit();

?>