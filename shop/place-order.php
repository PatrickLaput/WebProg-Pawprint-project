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
| GET CART
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        c.product_id,
        c.quantity,
        p.name,
        p.price,
        p.stock
    FROM cart_items c
    INNER JOIN products p
        ON c.product_id = p.prod_id
    WHERE c.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Check cart
|--------------------------------------------------------------------------
*/

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| GET DEFAULT ADDRESS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        full_name,
        phone,
        address_line,
        barangay,
        city,
        province,
        postal_code
    FROM addresses
    WHERE user_id = ?
    AND is_default = 1
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: checkout.php");
    exit();
}

$address = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| GET DEFAULT PAYMENT METHOD
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        payment_type
    FROM payment_methods
    WHERE user_id = ?
    AND is_default = 1
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    header("Location: checkout.php");
    exit();
}

$payment = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| CALCULATE TOTAL
|--------------------------------------------------------------------------
*/

$total = 0;

foreach ($cart_items as $item) {

    $quantity = (int)$item["quantity"];
    $price = (float)$item["price"];
    $stock = (int)$item["stock"];

    if ($quantity > $stock) {
        $quantity = $stock;
    }

    $total += $price * $quantity;
}


/*
|--------------------------------------------------------------------------
| START TRANSACTION
|--------------------------------------------------------------------------
*/

$conn->begin_transaction();

try {

    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO orders
        (
            user_id,
            total_amount,
            status,
            shipping_full_name,
            shipping_phone,
            shipping_address,
            shipping_barangay,
            shipping_city,
            shipping_province,
            shipping_postal_code,
            payment_type
        )
        VALUES
        (
            ?,
            ?,
            'Pending',
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $stmt->bind_param(
        "idssssssss",
        $user_id,
        $total,
        $address["full_name"],
        $address["phone"],
        $address["address_line"],
        $address["barangay"],
        $address["city"],
        $address["province"],
        $address["postal_code"],
        $payment["payment_type"]
    );

    if (!$stmt->execute()) {
        throw new Exception("Unable to create order.");
    }

    $order_id = $conn->insert_id;

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | SAVE ORDER ITEMS
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        INSERT INTO order_items
        (
            order_id,
            product_id,
            product_name,
            price,
            quantity
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    foreach ($cart_items as $item) {

        $product_id = (int)$item["product_id"];
        $product_name = $item["name"];
        $price = (float)$item["price"];
        $quantity = (int)$item["quantity"];

        $stmt->bind_param(
            "iisdi",
            $order_id,
            $product_id,
            $product_name,
            $price,
            $quantity
        );

        if (!$stmt->execute()) {
            throw new Exception("Unable to save order items.");
        }
    }

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | REMOVE PRODUCTS FROM CART
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        DELETE FROM cart_items
        WHERE user_id = ?
    ");

    $stmt->bind_param("i", $user_id);

    if (!$stmt->execute()) {
        throw new Exception("Unable to clear cart.");
    }

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | COMPLETE TRANSACTION
    |--------------------------------------------------------------------------
    */

    $conn->commit();


    /*
    |--------------------------------------------------------------------------
    | GO TO ORDER CONFIRMATION
    |--------------------------------------------------------------------------
    */

    header(
        "Location: orders.php?placed=success&order_id=" .
        $order_id
    );

    exit();

} catch (Exception $e) {

    $conn->rollback();

    die(
        "Unable to place order: " .
        htmlspecialchars($e->getMessage())
    );
}

?>