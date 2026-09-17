<?php
require_once "admin-auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="admin-dashboard.css">

    <title>Pawprint Admin Dashboard</title>

</head>
<body>

<header class="admin-header">

    <h1>Pawprint Admin Dashboard</h1>

    <a href="admin-logout.php" class="account-nav-item">
        <span class="nav-icon">↪</span>
        <span>Log Out</span>
    </a>
    
</header>

<main class="admin-container">

    <section class="admin-card">
        <h2>Product Management</h2>

        <a href="manage-products.php" class="admin-button">
            Manage Products
        </a>

    </section>

    <section class="admin-card">
        <h2>Review Management</h2>

        <a href="manage-reviews.php" class="admin-button">
            Manage Reviews
        </a>
        
    </section>

    <section class="admin-card">
    <h2>User Management</h2>

    <a href="manage-users.php" class="admin-button">
        View Users
    </a>
</section>

</main>

</body>
</html>