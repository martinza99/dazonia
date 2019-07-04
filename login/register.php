<?php
    if(!isset($_GET["token"])){
        header("Location: .");
        die("No token!");
    }
    require_once 'sql.php';
    require_once 'functions.php';
    require_once "../header.php";
?>
    <title>Register</title>
    <script src="login.js<?php echo "?$hash" ?>"></script>
</head>
<body>
    <form action="createAccount.php" method="POST" class="center">
        <input type="text" placeholder="Username" name="username" autocomplete="username" required><br>
        <input type="password" placeholder="Password" name="password" id="password" oninput="equals(this, '#passwordConfirm');" autocomplete="new-password" required><br>
        <input type="password" placeholder="confirm" id="passwordConfirm" oninput="equals(this, '#password');" autocomplete="new-password" required><br>
        <input type="text" value="<?php echo $_GET["token"];?>" name="token" hidden>
        <input type="submit" value="Register" id="submitButton" disabled>
    </form>
</body>
</html>