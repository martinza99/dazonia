<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';

    checkLogin();

    require_once "../header.php";
    echo '
        <title>Upload</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="center">
            <form action="../upload.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <input id="fileUp" name="file" type="file" accept=".png, .jpg, .gif, video/*" onchange="this.parentElement.submit();"><br>
                <input type="hidden" value="true" name="skip">
                <input type="submit">
            </form>
        </div>';
    require_once "../footer.php";
?>
</body>
</html>
