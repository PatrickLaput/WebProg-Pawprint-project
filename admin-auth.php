<?php
session_start();

if (
    !isset($_SESSION["admin_id"]) ||
    $_SESSION["is_admin"] !== true
) {
    header("Location: admin-login.php");
    exit;
}