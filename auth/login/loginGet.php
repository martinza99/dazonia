<?php
$status = [
    "incorrect" => "Incorrect Username or Password",
    "missing" => "No username or password given"
];
?>
<!DOCTYPE html>
<html>

<head>
    <?php include(__DIR__ . "/../../include/head.php"); ?>
    <title>Login</title>
    <link rel="stylesheet" href="/static/auth/form.css">
</head>

<body>
    <?php include(__DIR__ . "/../../include/nav.php"); ?>
    <main class="center">
        <?php if (isset($_GET["status"])) : ?>
            <span><?= $status[$_GET["status"]] ?></span>
        <?php endif; ?>
        <form action="." method="POST">
            <label for="username">Username:</label>
            <input type="text" placeholder="Username" name="username" autocomplete="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" placeholder="Password" name="password" autocomplete="current-password" id="password" required>
            <input type="text" value="<?= $_GET["fw"] ?? "" ?>" name="forward" hidden>
            <input type="submit" value="Login">
        </form>
    </main>

</body>

</html>