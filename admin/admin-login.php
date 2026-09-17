<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login/login.php");
    exit;
}

if ((int) ($_SESSION["is_admin"] ?? 0) !== 1) {
    header("Location: ../account/account.php");
    exit;
}

header("Location: admin-dashboard.php");
exit;
?>