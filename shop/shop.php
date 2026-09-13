<?php

 session_start();

require_once '../db.php';

/* =========================================================
   FILTERS
========================================================= */

$categories = [];

if (isset($_GET["category"])) {

    if (is_array($_GET["category"])) {
        $categories = $_GET["category"];
    } else {
        $categories[] = $_GET["category"];
    }
}


$pet_types = [];

if (isset($_GET["pet_type"])) {

    if (is_array($_GET["pet_type"])) {
        $pet_types = $_GET["pet_type"];
    } else {
        $pet_types[] = $_GET["pet_type"];
    }
}

/* Search */

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";


/* =========================================================
   PRICE
========================================================= */

$min_price = isset($_GET["min_price"])
    ? (float)$_GET["min_price"]
    : 0;

$max_price = isset($_GET["max_price"])
    ? (float)$_GET["max_price"]
    : 2500;


/* =========================================================
   SORT
========================================================= */

$sort = $_GET["sort"] ?? "best";


switch ($sort) {

    case "price_low":
        $order_by = "price ASC";
        break;

    case "price_high":
        $order_by = "price DESC";
        break;

    case "newest":
        $order_by = "created_at DESC";
        break;

    default:
        $order_by = "sales_count DESC";
        break;
}


/* =========================================================
   PAGINATION
========================================================= */

$per_page = 8;

$page = isset($_GET["page"])
    ? max(1, (int)$_GET["page"])
    : 1;

$offset = ($page - 1) * $per_page;


/* =========================================================
   BUILD FILTER QUERY
========================================================= */

$where = [];
$params = [];
$types = "";


/* Price */

$where[] = "price >= ?";
$params[] = $min_price;
$types .= "d";

$where[] = "price <= ?";
$params[] = $max_price;
$types .= "d";

/* Search */

if ($search !== "") {

    $where[] = "(name LIKE ? OR category LIKE ? OR pet_type LIKE ?)";

    $search_term = "%" . $search . "%";

    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;

    $types .= "sss";
}

/* Categories */

if (!empty($categories)) {

    $placeholders = implode(
        ",",
        array_fill(0, count($categories), "?")
    );

    $where[] = "category IN ($placeholders)";

    foreach ($categories as $category) {
        $params[] = $category;
        $types .= "s";
    }
}


/* Pet Type */

if (!empty($pet_types)) {

    $placeholders = implode(
        ",",
        array_fill(0, count($pet_types), "?")
    );

    $where[] = "pet_type IN ($placeholders)";

    foreach ($pet_types as $pet_type) {
        $params[] = $pet_type;
        $types .= "s";
    }
}


$where_sql = "";

if (!empty($where)) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}


/* =========================================================
   TOTAL PRODUCTS
========================================================= */

$count_sql = "
    SELECT COUNT(*) AS total
    FROM products
    $where_sql
";

$stmt = $conn->prepare($count_sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$count_result = $stmt->get_result();

$total_products = (int)$count_result->fetch_assoc()["total"];

$stmt->close();


$total_pages = max(
    1,
    ceil($total_products / $per_page)
);


/* =========================================================
   GET PRODUCTS
========================================================= */

$product_sql = "
    SELECT *
    FROM products
    $where_sql
    ORDER BY $order_by
    LIMIT ? OFFSET ?
";


$product_params = $params;
$product_types = $types . "ii";

$product_params[] = $per_page;
$product_params[] = $offset;


$stmt = $conn->prepare($product_sql);

$stmt->bind_param(
    $product_types,
    ...$product_params
);

$stmt->execute();

$result = $stmt->get_result();

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

$stmt->close();


/* =========================================================
   CATEGORY COUNTS
========================================================= */

$category_counts = [];

$category_result = $conn->query("
    SELECT category, COUNT(*) AS total
    FROM products
    GROUP BY category
");

while ($row = $category_result->fetch_assoc()) {

    $category_counts[$row["category"]] =
        $row["total"];
}


/* =========================================================
   PET TYPE COUNTS
========================================================= */

$pet_counts = [];

$pet_result = $conn->query("
    SELECT pet_type, COUNT(*) AS total
    FROM products
    GROUP BY pet_type
");

while ($row = $pet_result->fetch_assoc()) {

    $pet_counts[$row["pet_type"]] =
        $row["total"];
}


/* =========================================================
   URL HELPER
========================================================= */

function filter_url($page_number, $sort)
{
    $params = $_GET;

    $params["page"] = $page_number;
    $params["sort"] = $sort;

    return "shop.php?" . http_build_query($params);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <title>Pawprint Shop</title>

    <link rel="stylesheet" href="shop.css">

</head>

<body>

        <header class="site-header">
            <div class="nav">

                <div class="logo">
                    <a href="../index.php">
                        <img src="../images/logo.png" alt="Pawprint Logo">
                    </a>
                </div>

                <nav class="nav-links">
                    <a href="../index.php">Home</a>
                    <a href="shop.php">Shop</a>
                    <a href="../about us/about-us.php">About Us</a>
                    <a href="../contat us/contact.php">Contact</a>
                </nav>

                <div class="nav-icons">
                    <a href="../cart/cart.php">
                        <img src="../images/cart.png" alt="Cart">
                    </a>
                    <a href="#" class="search-toggle" aria-label="Search">
                        <img src="../images/search.png" alt="search">
                    </a>
                    <a href="../account/account.php">
                        <img src="../images/acc.png" alt="Account">
                    </a>
                </div>
            </div>
        </header>

        <div class="search-overlay" id="search-overlay">

            <div class="search-box">

                <form action="shop.php" method="GET" id="search-form">

                    <input
                        type="text"
                        name="search"
                        id="search-input"
                        placeholder="Search products..."
                        autocomplete="off"
                    >

                    <button type="submit" aria-label="Search">
                        <img src="../images/search.png" alt="Search">
                    </button>

                </form>

            </div>

        </div>

        <section class="shop-section">

            <div class="shop-container">


                <!-- =================================================
                    FILTERS
                ================================================== -->

                <aside class="shop-filters">

                    <h2>Filters</h2>


                    <form method="GET"
                        action="shop.php"
                        id="filter-form">


                        <!-- Categories -->

                        <div class="filter-section">

                            <h3>Categories</h3>


                            <?php

                            $category_list = [
                                "Pet Food",
                                "Toys",
                                "Accessories",
                                "Beds & Furniture",
                                "Grooming",
                                "Health & Wellness"
                            ];

                            foreach ($category_list as $category):

                                $checked =
                                    in_array(
                                        $category,
                                        $categories
                                    );

                            ?>

                            <label class="filter-option">

                                <input
                                    type="checkbox"
                                    name="category[]"
                                    value="<?php echo htmlspecialchars($category); ?>"
                                    <?php echo $checked ? "checked" : ""; ?>
                                >

                                <span class="filter-name">
                                    <?php echo htmlspecialchars($category); ?>
                                </span>

                                <span class="filter-count">
                                    (<?php echo $category_counts[$category] ?? 0; ?>)
                                </span>

                            </label>

                            <?php endforeach; ?>

                        </div>


                        <!-- Pet Type -->

                        <div class="filter-section">

                            <h3>Pet Type</h3>


                            <?php

                            $pet_list = [
                                "Dogs",
                                "Cats",
                                "Both"
                            ];

                            foreach ($pet_list as $pet):

                                $checked =
                                    in_array(
                                        $pet,
                                        $pet_types
                                    );

                            ?>

                            <label class="filter-option">

                                <input
                                    type="checkbox"
                                    name="pet_type[]"
                                    value="<?php echo htmlspecialchars($pet); ?>"
                                    <?php echo $checked ? "checked" : ""; ?>
                                >

                                <span class="filter-name">
                                    <?php echo htmlspecialchars($pet); ?>
                                </span>

                                <span class="filter-count">
                                    (<?php echo $pet_counts[$pet] ?? 0; ?>)
                                </span>

                            </label>

                            <?php endforeach; ?>

                        </div>


                        <!-- Price -->

                        <div class="filter-section">

                            <h3>Price Range</h3>

                            <div class="price-values">

                                <span id="min-price-label">
                                    ₱<?php echo number_format($min_price, 0); ?>
                                </span>

                                <span id="max-price-label">
                                    ₱<?php echo number_format($max_price, 0); ?>
                                </span>

                            </div>


                            <div class="price-slider">

                                <input
                                    type="range"
                                    name="min_price"
                                    id="min-price"
                                    min="0"
                                    max="2500"
                                    value="<?php echo $min_price; ?>"
                                >

                                <input
                                    type="range"
                                    name="max_price"
                                    id="max-price"
                                    min="0"
                                    max="2500"
                                    value="<?php echo $max_price; ?>"
                                >

                            </div>

                        </div>


                        <input
                            type="hidden"
                            name="sort"
                            value="<?php echo htmlspecialchars($sort); ?>"
                        >


                        <button
                            type="submit"
                            class="apply-filter-btn"
                        >
                            Apply Filters
                        </button>

                    </form>

                </aside>


                <!-- =================================================
                    PRODUCTS
                ================================================== -->

                <div class="shop-products">


                    <!-- HEADER -->

                    <div class="products-header">

                        <div>

                            <h1>All Products</h1>

                            <?php

                            $start =
                                $total_products > 0
                                    ? $offset + 1
                                    : 0;

                            $end =
                                min(
                                    $offset + $per_page,
                                    $total_products
                                );

                            ?>

                            <p>
                                Showing
                                <?php echo $start; ?>
                                –
                                <?php echo $end; ?>
                                of
                                <?php echo $total_products; ?>
                                products
                            </p>

                        </div>


                        <!-- SORT -->

                        <form method="GET"
                            action="shop.php">

                            <?php foreach ($categories as $category): ?>

                                <input
                                    type="hidden"
                                    name="category[]"
                                    value="<?php echo htmlspecialchars($category); ?>"
                                >

                            <?php endforeach; ?>


                            <?php foreach ($pet_types as $pet): ?>

                                <input
                                    type="hidden"
                                    name="pet_type[]"
                                    value="<?php echo htmlspecialchars($pet); ?>"
                                >

                            <?php endforeach; ?>


                            <input
                                type="hidden"
                                name="min_price"
                                value="<?php echo $min_price; ?>"
                            >

                            <input
                                type="hidden"
                                name="max_price"
                                value="<?php echo $max_price; ?>"
                            >


                            <select
                                class="sort-select"
                                name="sort"
                                onchange="this.form.submit()"
                            >

                                <option
                                    value="best"
                                    <?php echo $sort === "best" ? "selected" : ""; ?>
                                >
                                    Sort by: Best Selling
                                </option>

                                <option
                                    value="price_low"
                                    <?php echo $sort === "price_low" ? "selected" : ""; ?>
                                >
                                    Price: Low to High
                                </option>

                                <option
                                    value="price_high"
                                    <?php echo $sort === "price_high" ? "selected" : ""; ?>
                                >
                                    Price: High to Low
                                </option>

                                <option
                                    value="newest"
                                    <?php echo $sort === "newest" ? "selected" : ""; ?>
                                >
                                    Newest
                                </option>

                            </select>

                        </form>

                    </div>


                    <!-- PRODUCT GRID -->

                    <div class="product-grid">


                        <?php if (empty($products)): ?>

                            <div class="no-products">

                                <h2>No products found</h2>

                                <p>
                                    Try changing your filters.
                                </p>

                            </div>


                        <?php else: ?>


                            <?php foreach ($products as $product): ?>


                                <div class="product-card">


                                    <!-- IMAGE -->

                                    <div class="product-image">

                                        <?php if (
                                            !empty($product["image"]) &&
                                            file_exists($product["image"])
                                        ): ?>

                                            <img
                                                src="<?php echo htmlspecialchars($product["image"]); ?>"
                                                alt="<?php echo htmlspecialchars($product["name"]); ?>"
                                            >

                                        <?php else: ?>

                                            <img src="../products/petfood.png">

                                        <?php endif; ?>

                                    </div>


                                    <!-- DETAILS -->

                                    <div class="product-details">

                                        <h3>
                                            <?php echo htmlspecialchars($product["name"]); ?>
                                        </h3>


                                        <div class="rating">

                                            <span>
                                                ★★★★★
                                            </span>

                                            <small>
                                                (<?php echo $product["review_count"]; ?>)
                                            </small>

                                        </div>


                                        <p class="product-price">

                                            ₱<?php
                                            echo number_format(
                                                $product["price"],
                                                2
                                            );
                                            ?>

                                        </p>


                                        <?php if ($product["stock"] > 0): ?>

                                            <form
                                                method="POST"
                                                action="add-to-cart.php"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="product_id"
                                                    value="<?php echo $product["prod_id"]; ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="quantity"
                                                    value="1"
                                                >

                                                <button
                                                    type="submit"
                                                    class="add-cart"
                                                >
                                                    Add to Cart
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <button
                                                class="add-cart out-of-stock"
                                                disabled
                                            >
                                                Out of Stock
                                            </button>

                                        <?php endif; ?>

                                    </div>

                                </div>


                            <?php endforeach; ?>


                        <?php endif; ?>

                    </div>


                    <!-- =================================================
                        PAGINATION
                    ================================================== -->

                    <?php if ($total_pages > 1): ?>

                        <div class="pagination">


                            <!-- PREVIOUS -->

                            <?php if ($page > 1): ?>

                                <a
                                    href="<?php echo filter_url($page - 1, $sort); ?>"
                                    class="page-arrow"
                                >
                                    ‹
                                </a>

                            <?php endif; ?>


                            <!-- PAGE NUMBERS -->

                            <?php for (
                                $i = 1;
                                $i <= $total_pages;
                                $i++
                            ): ?>

                                <a
                                    href="<?php echo filter_url($i, $sort); ?>"
                                    class="page-number
                                    <?php echo $i == $page ? "active" : ""; ?>"
                                >
                                    <?php echo $i; ?>
                                </a>

                            <?php endfor; ?>


                            <!-- NEXT -->

                            <?php if ($page < $total_pages): ?>

                                <a
                                    href="<?php echo filter_url($page + 1, $sort); ?>"
                                    class="page-arrow"
                                >
                                    ›
                                </a>

                            <?php endif; ?>


                        </div>

                    <?php endif; ?>


                </div>

            </div>

        </section>

        <footer class="footer">

            <div class="footer-container">
                <div class="footer-brand">

                    <img src="../images/logo-white.png" alt="Pawprint" class="footer-logo">
                    <p>
                        Quality pet food, toys, and accessories<br>
                        made for happy pets and happier<br>
                        pet parents.
                    </p>

                    <div class="footer-socials">
                        <a href="https://youtube.com" aria-label="YouTube">
                            <img src="../images/yt-ico.png" alt="YouTube">
                        </a>
                        <a href="https://facebook.com" aria-label="Facebook">
                            <img src="../images/fb-ico.png" alt="Facebook">
                        </a>
                        <a href="https://instagram.com" aria-label="Instagram">
                            <img src="../images/ig-ico.png" alt="Instagram">
                        </a>
                        <a href="https://tiktok.com" aria-label="TikTok">
                            <img src="../images/tk-ico.png" alt="TikTok">
                        </a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <a href="shop.php">Shop</a>
                    <a href="../about us/about-us.php">About Us</a>
                    <a href="../contact us/contact.php">Contact Us</a>
                </div>

                <div class="footer-column">
                    <h3>Customer Care</h3>
                    <a href="../account/account.php">My Account</a>
                    <a href="../terms&privacy.php">Terms & Conditions</a>
                    <a href="../terms&privacy.php">Privacy Policy</a>
                </div>

                <div class="footer-newsletter">
                    <h3>Stay in the Loop</h3>
                    <p>
                        Get updates on new products,<br>
                        exclusive deals, and pet care tips!
                    </p>
                    <form class="subscribe-form">

                        <input type="email" placeholder="Enter you email" required>
                        <button type="submit">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <img src="../images/pawprint-brown.png" alt="" class="footer-paw">

        </footer>

<script src="shop.js"></script>

</body>
</html>