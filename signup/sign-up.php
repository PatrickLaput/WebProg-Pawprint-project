<?php

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $birthdate = $_POST["birthdate"];

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Check if email already exists
    $check = $conn->prepare(
        "SELECT userID FROM users WHERE email = ?"
    );

    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("An account with this email already exists.");
    }

    $check->close();

    // Hash the password
    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Insert user into database
    $stmt = $conn->prepare(
        "INSERT INTO users 
        (first_name, last_name, email, password, birthdate)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssss",
        $first_name,
        $last_name,
        $email,
        $hashed_password,
        $birthdate
    );

    if ($stmt->execute()) {

        // Account successfully created
        header("Location: signup.php?signup=success");
        exit();

    } else {

        echo "Error creating account.";
    }

    $stmt->close();
    $conn->close();
}

?>