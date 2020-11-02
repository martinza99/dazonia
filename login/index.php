<?php
require_once "sql.php";
require_once 'functions.php';
require_once "../header.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <form action="loginPost.php" method="POST" class="center">
        <input type="text" placeholder="Username" name="username" autocomplete="username" required><br>
        <input type="password" placeholder="Password" name="password" autocomplete="current-password" required><br>
        <input type="text" value="<?= $_GET["fw"] ?? "" ?>" name="forward" hidden>
        <input type="submit">
    </form>
</body>

</html>