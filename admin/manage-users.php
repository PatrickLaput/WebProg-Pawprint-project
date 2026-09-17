<?php
require_once "admin-auth.php";
require_once "../db.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* Delete user account */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_user"])) {
    $user_id = (int) $_POST["user_id"];

    // Prevent an admin from deleting their own account
    if ($user_id !== (int) $_SESSION["user_id"]) {
        $stmt = $conn->prepare("DELETE FROM users WHERE userId = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage-users.php");
    exit;
}

/* Retrieve all users */
$stmt = $conn->prepare("
    SELECT userId, first_name, last_name, email, birthdate, created_at, is_admin
    FROM users
    ORDER BY created_at DESC
");

$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="manage-users.css">

    <title>Manage Users | Pawprint</title>
</head>
<body>

<header class="admin-header">
    <h1>Manage Users</h1>

    <a href="admin-dashboard.php">Back to Dashboard</a>
</header>

<main class="admin-container">

    <section class="admin-card user-management-card">
        <h2>Registered Users</h2>

        <div class="user-table-wrapper">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Birthdate</th>
                        <th>Created At</th>
                        <th>Account Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($user["userId"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $user["first_name"] . " " . $user["last_name"]
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user["email"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user["birthdate"] ?? "N/A") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user["created_at"] ?? "N/A") ?>
                            </td>

                            <td>
                                <?php if ((int) $user["is_admin"] === 1): ?>
                                    Admin
                                <?php else: ?>
                                    User
                                <?php endif; ?>
                            </td>

                            <td class="user-actions">
                                <a
                                    href="user-orders.php?user_id=<?= (int) $user["userId"] ?>"
                                    class="view-orders-button"
                                >
                                    View Orders
                                </a>

                                <?php if ((int) $user["userId"] !== (int) $_SESSION["user_id"]): ?>
                                    <form method="POST" onsubmit="return confirm('Delete this account?');">
                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= (int) $user["userId"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="delete_user"
                                            class="delete-user-button"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="current-account">
                                        Current Account
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

</body>
</html>