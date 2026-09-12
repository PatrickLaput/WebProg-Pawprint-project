<?php

session_start();
require_once "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Please sign in first."
    ]);
    exit();
}

$user_id = (int)$_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| Only accept POST requests
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit();
}


/*
|--------------------------------------------------------------------------
| Get product ID and quantity
|--------------------------------------------------------------------------
*/

$product_id = isset($_POST["product_id"])
    ? (int)$_POST["product_id"]
    : 0;

$quantity = isset($_POST["quantity"])
    ? (int)$_POST["quantity"]
    : 1;

if ($product_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid product."
    ]);
    exit();
}

if ($quantity <= 0) {
    $quantity = 1;
}


/*
|--------------------------------------------------------------------------
| Find product
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        prod_id,
        name,
        stock
    FROM products
    WHERE prod_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "Product not found."
    ]);
    exit();
}

$product = $result->fetch_assoc();

$stmt->close();

$stock = (int)$product["stock"];


/*
|--------------------------------------------------------------------------
| Check stock
|--------------------------------------------------------------------------
*/

if ($stock <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "This product is out of stock."
    ]);
    exit();
}


/*
|--------------------------------------------------------------------------
| Check if product is already in cart
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        id,
        quantity
    FROM cart_items
    WHERE user_id = ?
    AND product_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Update existing cart item
|--------------------------------------------------------------------------
*/

if ($result->num_rows > 0) {

    $cart_item = $result->fetch_assoc();

    $cart_id = (int)$cart_item["id"];
    $current_quantity = (int)$cart_item["quantity"];

    $new_quantity = $current_quantity + $quantity;

    if ($new_quantity > $stock) {
        $new_quantity = $stock;
    }

    $stmt->close();

    $stmt = $conn->prepare("
        UPDATE cart_items
        SET quantity = ?
        WHERE id = ?
        AND user_id = ?
    ");

    $stmt->bind_param(
        "iii",
        $new_quantity,
        $cart_id,
        $user_id
    );

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();

        echo json_encode([
            "success" => false,
            "message" => "Unable to update cart."
        ]);
        exit();
    }

    $stmt->close();


    echo json_encode([
        "success" => true,
        "message" => "Product added to cart.",
        "quantity" => $new_quantity
    ]);

    exit();
}


/*
|--------------------------------------------------------------------------
| Add new cart item
|--------------------------------------------------------------------------
*/

if ($quantity > $stock) {
    $quantity = $stock;
}

$stmt->close();

$stmt = $conn->prepare("
    INSERT INTO cart_items
    (
        user_id,
        product_id,
        quantity
    )
    VALUES
    (
        ?,
        ?,
        ?
    )
");

$stmt->bind_param(
    "iii",
    $user_id,
    $product_id,
    $quantity
);

if (!$stmt->execute()) {
    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "Unable to add product to cart."
    ]);
    exit();
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "message" => "Product added to cart.",
    "quantity" => $quantity
]);

exit();

?>