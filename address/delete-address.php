<?php

session_start();
require_once "db.php";


// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit();
}

$user_id = (int)$_SESSION["user_id"];


// Get address ID
if (!isset($_GET["userID"]) || !is_numeric($_GET["userID"])) {
    header("Location: addresses.php");
    exit();
}

$address_id = (int)$_GET["userID"];


// Make sure this address belongs to the logged-in user
$stmt = $conn->prepare("
    SELECT is_default
    FROM addresses
    WHERE userID = ? AND user_id = ?
");

$stmt->bind_param("ii", $address_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: addresses.php?deleted=notfound");
    exit();
}

$address = $result->fetch_assoc();

$was_default = (int)$address["is_default"];

$stmt->close();


// Delete the address
$stmt = $conn->prepare("
    DELETE FROM addresses
    WHERE userID = ? AND user_id = ?
");

$stmt->bind_param("ii", $address_id, $user_id);

if (!$stmt->execute()) {

    $stmt->close();

    die("Unable to delete address: " . $conn->error);
}

$stmt->close();


// If the deleted address was the default,
// make another address the default.
if ($was_default === 1) {

    $stmt = $conn->prepare("
        SELECT userID
        FROM addresses
        WHERE user_id = ?
        ORDER BY userID DESC
        LIMIT 1
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $new_default = $result->fetch_assoc();

        $new_default_id = (int)$new_default["userID"];

        $stmt->close();


        $stmt = $conn->prepare("
            UPDATE addresses
            SET is_default = 1
            WHERE userID = ? AND user_id = ?
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


// Go back to addresses
header("Location: addresses.php?deleted=success");
exit();

?>