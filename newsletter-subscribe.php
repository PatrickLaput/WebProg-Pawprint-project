<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    $email = trim($_POST["email"]);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $folder = __DIR__ . "/subscribers";

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        file_put_contents(
            $folder . "/subscribers.txt",
            $email . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
?>