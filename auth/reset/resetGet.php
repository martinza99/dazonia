<?php
$status = [
    "success" => "Successfully changed password",
    "wrong" => "Wrong password",
    "invalid" => "Invalid reset key",
    "missing" => "Username or Password or Token missing"
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include(__DIR__ . "/../../include/head.php"); ?>
    <title>Password Reset</title>
    <link rel="stylesheet" href="/static/auth/form.css">
</head>

<body>
    <?php include(__DIR__ . "/../../include/nav.php"); ?>
    <main class="center">
        <?php if (isset($_GET["status"])) : ?>
            <span><?= $status[$_GET["status"]] ?></span>
        <?php endif; ?>
        <span>❗ Your API key will be reset</span>
        <form action="." method="post">
            <?php if (!isset($_GET["resetKey"])) : ?>
                <label for="cPassword">Current Password: </label>
                <input type="password" placeholder="current password" name="currentPassword" autocomplete="current-password" id="cPassword" required>
            <?php endif; ?>
            <label for="nPassword">New Password: </label>
            <input type="password" placeholder="new password" name="newPassword" id="password" autocomplete="new-password" id="nPassword" required>
            <?php if (isset($_GET["resetKey"])) : ?>
                <input type="hidden" name="resetKey" value="$_GET[resetKey]">
            <?php endif; ?>
            <input type="submit" value="Change" id="submitButton">
        </form>
    </main>
    <?php include(__DIR__ . "/../../include/footer.php"); ?>
</body>

</html>