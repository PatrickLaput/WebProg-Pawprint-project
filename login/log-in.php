<?php

session_start();

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user by email
    $stmt = $conn->prepare(
        "SELECT userId, first_name, last_name, email, password
         FROM users
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"])) {

            // Login successful
            $_SESSION["user_id"] = $user["userId"];
            $_SESSION["first_name"] = $user["first_name"];
            $_SESSION["last_name"] = $user["last_name"];
            $_SESSION["email"] = $user["email"];

            header("Location: account.php");
            exit();

        } else {

            echo "Incorrect password.";
        }

    } else {

        echo "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}

?>