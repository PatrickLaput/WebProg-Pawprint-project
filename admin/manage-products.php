<?php
require_once "../admin-auth.php";
require_once "../db.php";

$message = "";
$error = "";

/*
|--------------------------------------------------------------------------
| Upload product image
|--------------------------------------------------------------------------
*/
function uploadProductImage($file)
{
    if (!isset($file) || $file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif"
    ];

    if (!in_array($file["type"], $allowedTypes)) {
        return false;
    }

    $uploadDirectory = "../products/";

    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $extension = strtolower(
        pathinfo($file["name"], PATHINFO_EXTENSION)
    );

    $newFileName = uniqid("product_", true) . "." . $extension;
    $targetPath = $uploadDirectory . $newFileName;

    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        return "products/" . $newFileName;
    }

    return false;
}

/*
|--------------------------------------------------------------------------
| Add product
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_product"])) {
    $prodName = trim($_POST["prod_name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $category = trim($_POST["category"]);
    $petType = trim($_POST["pet_type"]);

    $imagePath = uploadProductImage($_FILES["image"] ?? null);

    if ($prodName === "" || $price < 0 || $stock < 0) {
        $error = "Please provide valid product information.";
    } elseif ($_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE && $imagePath === false) {
        $error = "Invalid image file. Please upload JPG, PNG, WEBP, or GIF.";
    } else {
        $sql = "
            INSERT INTO products
            (
                name,
                description,
                price,
                stock,
                category,
                pet_type,
                image
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param(
                "ssdisss",
                $prodName,
                $description,
                $price,
                $stock,
                $category,
                $petType,
                $imagePath
            );

            if ($stmt->execute()) {
                $message = "Product added successfully.";
            } else {
                $error = "Unable to add product.";
            }

            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Update product
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_product"])) {
    $prodId = intval($_POST["prod_id"]);
    $prodName = trim($_POST["prod_name"]);
    $description = trim($_POST["description"]);
    $price = floatval($_POST["price"]);
    $stock = intval($_POST["stock"]);
    $category = trim($_POST["category"]);
    $petType = trim($_POST["pet_type"]);

    $newImagePath = uploadProductImage($_FILES["image"] ?? null);

    if ($prodName === "" || $price < 0 || $stock < 0) {
        $error = "Please provide valid product information.";
    } elseif ($_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE && $newImagePath === false) {
        $error = "Invalid image file. Please upload JPG, PNG, WEBP, or GIF.";
    } else {
        if ($newImagePath !== null) {
            $sql = "
                UPDATE products
                SET
                    prod_name = ?,
                    description = ?,
                    price = ?,
                    stock = ?,
                    category = ?,
                    pet_type = ?,
                    image = ?
                WHERE prod_id = ?
            ";

            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(
                    "ssdisssi",
                    $prodName,
                    $description,
                    $price,
                    $stock,
                    $category,
                    $petType,
                    $newImagePath,
                    $prodId
                );
            }
        } else {
            $sql = "
                UPDATE products
                SET
                    prod_name = ?,
                    description = ?,
                    price = ?,
                    stock = ?,
                    category = ?,
                    pet_type = ?
                WHERE prod_id = ?
            ";

            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param(
                    "ssdissi",
                    $prodName,
                    $description,
                    $price,
                    $stock,
                    $category,
                    $petType,
                    $prodId
                );
            }
        }

        if (isset($stmt) && $stmt->execute()) {
            $message = "Product updated successfully.";
        } else {
            $error = "Unable to update product.";
        }

        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

/*
|--------------------------------------------------------------------------
| Delete product
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_product"])) {
    $prodId = intval($_POST["prod_id"]);

    $sql = "DELETE FROM products WHERE prod_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $prodId);

        if ($stmt->execute()) {
            $message = "Product deleted successfully.";
        } else {
            $error = "Unable to delete product.";
        }

        $stmt->close();
    }
}

/*
|--------------------------------------------------------------------------
| Get product for editing
|--------------------------------------------------------------------------
*/
$editProduct = null;

if (isset($_GET["edit"])) {
    $editId = intval($_GET["edit"]);

    $sql = "SELECT * FROM products WHERE prod_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $editId);
        $stmt->execute();

        $result = $stmt->get_result();
        $editProduct = $result->fetch_assoc();

        $stmt->close();
    }
}

/*
|--------------------------------------------------------------------------
| Get all products
|--------------------------------------------------------------------------
*/
$products = [];

$result = $conn->query("
    SELECT *
    FROM products
    ORDER BY prod_id DESC
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="manage-products.css">

    <title>Pawprint Admin</title>

</head>

<body>

<header>
    <h1>Pawprint Admin - Manage Products</h1>
    <a href="admin-dashboard.php">Back to Dashboard</a>
</header>

<div class="container">

    <?php if ($message !== ""): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <section class="form-card">
        <h2>
            <?= $editProduct ? "Edit Product" : "Add New Product" ?>
        </h2>

        <form method="POST" enctype="multipart/form-data">

            <?php if ($editProduct): ?>
                <input
                    type="hidden"
                    name="prod_id"
                    value="<?= (int) $editProduct["prod_id"] ?>"
                >
            <?php endif; ?>

            <div class="form-grid">

                <div class="form-group">
                    <label for="prod_name">Product Name</label>
                    <input
                        type="text"
                        id="prod_name"
                        name="prod_name"
                        required
                        value="<?= htmlspecialchars($editProduct["prod_name"] ?? "") ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        min="0"
                        step="0.01"
                        required
                        value="<?= htmlspecialchars($editProduct["price"] ?? "") ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input
                        type="number"
                        id="stock"
                        name="stock"
                        min="0"
                        required
                        value="<?= htmlspecialchars($editProduct["stock"] ?? "") ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Food"
                            <?= (($editProduct["category"] ?? "") === "Food") ? "selected" : "" ?>>
                            Food
                        </option>
                        <option value="Toys"
                            <?= (($editProduct["category"] ?? "") === "Toys") ? "selected" : "" ?>>
                            Toys
                        </option>
                        <option value="Accessories"
                            <?= (($editProduct["category"] ?? "") === "Accessories") ? "selected" : "" ?>>
                            Accessories
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pet_type">Pet Type</label>
                    <select id="pet_type" name="pet_type" required>
                        <option value="">Select Pet Type</option>
                        <option value="Dog"
                            <?= (($editProduct["pet_type"] ?? "") === "Dog") ? "selected" : "" ?>>
                            Dog
                        </option>
                        <option value="Cat"
                            <?= (($editProduct["pet_type"] ?? "") === "Cat") ? "selected" : "" ?>>
                            Cat
                        </option>
                        <option value="Other"
                            <?= (($editProduct["pet_type"] ?? "") === "Other") ? "selected" : "" ?>>
                            Other
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">
                        Product Image
                        <?= $editProduct ? "(optional)" : "" ?>
                    </label>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        <?= $editProduct ? "" : "required" ?>
                    >
                </div>

                <div class="form-group full">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        required
                    ><?= htmlspecialchars($editProduct["description"] ?? "") ?></textarea>
                </div>

            </div>

            <div class="buttons">
                <?php if ($editProduct): ?>
                    <button
                        type="submit"
                        name="update_product"
                        class="save-button"
                    >
                        Update Product
                    </button>

                    <a href="manage-products.php" class="cancel-button">
                        Cancel
                    </a>
                <?php else: ?>
                    <button
                        type="submit"
                        name="add_product"
                        class="save-button"
                    >
                        Add Product
                    </button>
                <?php endif; ?>
            </div>

        </form>
    </section>

    <section class="table-card">
        <h2>Product List</h2>

        <div class="table-wrapper">
            <?php if (count($products) > 0): ?>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Pet Type</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?= (int) $product["prod_id"] ?>
                                </td>

                                <td>
                                    <?php if (!empty($product["image"])): ?>
                                        <img
                                            class="product-image"
                                            src="../<?= htmlspecialchars($product["image"]) ?>"
                                        >
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($product["name"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($product["category"]) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($product["pet_type"]) ?>
                                </td>

                                <td>
                                    ₱<?= number_format((float) $product["price"], 2) ?>
                                </td>

                                <td>
                                    <?= (int) $product["stock"] ?>
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <a
                                            href="manage-products.php?edit=<?= (int) $product["prod_id"] ?>"
                                            class="edit-button button"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this product?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="prod_id"
                                                value="<?= (int) $product["prod_id"] ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="delete_product"
                                                class="delete-button"
                                            >
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <div class="empty">
                    No products found.
                </div>
            <?php endif; ?>
        </div>
    </section>

</div>

</body>
</html>