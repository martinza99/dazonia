<?php
    if(!isset($_GET["token"])){
        header("Location: .");
        die("No token!");
    }
    require_once "../header.php";
?>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css" />
</head>
<body>
    <form action="createAccount.php" method="POST" class="center">
        <input type="text" placeholder="Username" name="username" require_onced><br>
        <input type="password" placeholder="Password" name="password" require_onced><br>
        <input type="password" placeholder="confirm" name="password2" require_onced><br>
        <input type="text" value="<?php echo $_GET["token"];?>" name="token" hidden>
        <input type="submit">
    </form>
</body>
</html>