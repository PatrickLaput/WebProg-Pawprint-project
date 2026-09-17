<?php
session_start();
require_once "../db.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    try {
        $stmt = $conn->prepare("
            SELECT userID, first_name, last_name, email, password, is_admin
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (
            $user &&
            (int) $user["is_admin"] === 1 &&
            password_verify($password, $user["password"])
        ) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $user["userID"];
            $_SESSION["admin_username"] =
                trim($user["first_name"] . " " . $user["last_name"]);
            $_SESSION["is_admin"] = true;

            header("Location: admin-dashboard.php");
            exit;
        }

        $error = "Invalid administrator email or password.";
    } catch (Throwable $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-login.css">
    <title>Admin Login</title>
</head>
<body>

<div class="admin-login-container">
    <h2>Admin Login</h2>

    <?php if ($error !== ""): ?>
        <p class="error-message">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <button type="submit" class="login-button">
            Login
        </button>
    </form>
</div>

</body>
</html>