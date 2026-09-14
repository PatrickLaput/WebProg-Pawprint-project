<?php
session_start();

require_once __DIR__ . '/../db.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please enter your email and password."
    ]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT userId, first_name, last_name, email, password
     FROM users
     WHERE email = ?"
);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error. Please try again."
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["userId"];
        $_SESSION["first_name"] = $user["first_name"];
        $_SESSION["last_name"] = $user["last_name"];
        $_SESSION["email"] = $user["email"];

        echo json_encode([
            "success" => true,
            "redirect" => "../account/account.php"
        ]);

        $stmt->close();
        $conn->close();
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "Incorrect email or password."
]);

$stmt->close();
$conn->close();
?>