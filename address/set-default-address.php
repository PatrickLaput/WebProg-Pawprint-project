<?php

 
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../auth.php';

$user_id = (int)$_SESSION["user_id"];


// Get address ID
if (!isset($_GET["userID"]) || !is_numeric($_GET["userID"])) {
    header("Location: addresses.php");
    exit();
}

$address_id = (int)$_GET["userID"];


// Check if the address belongs to the logged-in user
$stmt = $conn->prepare("
    SELECT userID
    FROM addresses
    WHERE userID = ? AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $address_id,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


// Address doesn't exist or belongs to another user
if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: addresses.php?default=notfound");
    exit();
}

$stmt->close();


// Remove default status from all of this user's addresses
$stmt = $conn->prepare("
    UPDATE addresses
    SET is_default = 0
    WHERE user_id = ?
");

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$stmt->close();


// Set the selected address as default
$stmt = $conn->prepare("
    UPDATE addresses
    SET is_default = 1
    WHERE userID = ? AND user_id = ?
");

$stmt->bind_param(
    "ii",
    $address_id,
    $user_id
);


if ($stmt->execute()) {

    $stmt->close();

    header("Location: addresses.php?default=success");
    exit();

} else {

    $error = $conn->error;

    $stmt->close();

    die("Unable to set default address: " . $error);
}

?>