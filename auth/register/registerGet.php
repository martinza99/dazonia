<?php
$status = [
    "taken" => "Username already taken",
    "invalid" => "Invalid token",
    "missing" => "Not logged in and no key given!"
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include(__DIR__ . "/../../include/head.php"); ?>
    <title>Register</title>
    <link rel="stylesheet" href="/static/auth/form.css">
</head>

<body>
    <?php include(__DIR__ . "/../../include/nav.php"); ?>
    <main class="center">
        <?php if (isset($_GET["status"])) : ?>
            <span><?= $status[$_GET["status"]] ?></span>
        <?php endif; ?>
        <form action="." method="POST">
            <label for="username">Username: </label>
            <input type="text" placeholder="Username" name="username" autocomplete="username" id="username" required>
            <label for="password">Password: </label>
            <input type="password" placeholder="Password" name="password" id="password" autocomplete="new-password" required>
            <label for="token">Token: </label>
            <input type="text" placeholder="Token" value="<?= $_GET["token"] ?? "" ?>" name="token" id="token" required>
            <input type="submit" value="Register">
        </form>
    </main>
</body>

</html>