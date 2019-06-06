<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    if($_SESSION["userId"]>1||!checkLogin($_SESSION["userId"])){
        header("Location: .");
        die();
    }
    $userId = $_SESSION["userId"];

    if(isset($_POST["action"])){
        switch ($_POST["action"]){
            case "sql": 
                $sql = $_POST["sql"];
                $result = $conn->query($sql);
                break;
            case "u":
                exec('git pull https://github.com/martinza99/dazonia.git master');
                header("Location: remote.php");
                break;
        }
    }
    require_once "../header.php";
?>

    <title>Remote Server Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <form action="remote.php" method="POST" autocomplete="off">
        <input type="hidden" name="action" value="sql">
        <textarea name="sql" cols="50" rows="5" placeholder="SQL query"></textarea><br>
        <input type="submit" value="Submit Query">
    </form>
    
<?php
    require_once "../footer.php"; 
?>

</body>
</html>