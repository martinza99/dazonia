<?php
    require_once "../login/sql.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css<?php echo "?$hash" ?>" />
</head>
<body>
    <form action="loginPost.php" method="POST" class="center">
        <input type="text" placeholder="Username" name="username" require_onced><br>
        <input type="password" placeholder="Password" name="password" require_onced><br>
        <input type="text" value="true" name="forward" hidden>
        <input type="submit">
    </form>
</body>
</html>