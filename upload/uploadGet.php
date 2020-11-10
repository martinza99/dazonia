<?php
require_once(__DIR__ . "/../include/functions.php");
checkLogin();

?>

<head>
    <?php include(__DIR__ . "/../include/head.php"); ?>
    <title>Upload</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <script src="upload.js"></script> -->
</head>

<body>
    <?php include(__DIR__ . "/../include/nav.php"); ?>
    <main class="center">
        <form action="." method="POST" enctype="multipart/form-data" autocomplete="off">
            <input id="fileUp" name="file[]" type="file" accept=".png, .jpg, .gif, video/*" multiple><br>
            <input type="hidden" value="true" name="skip">
            <input type="submit">
        </form>
    </main>
    <?php include(__DIR__ . "/../include/footer.php"); ?>
</body>

</html>