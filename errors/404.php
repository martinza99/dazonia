<?php require_once("../include/functions.php"); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../include/head.php"); ?>
    <title>404 Not Found</title>
</head>

<body>
    <?php include("../include/nav.php"); ?>
    <h1>Can not find [<?= $_SERVER["REDIRECT_REQUEST_METHOD"] ?>] <?= $_SERVER["REQUEST_URI"] ?></h1>
    <?php include("../include/footer.php"); ?>
</body>

</html>