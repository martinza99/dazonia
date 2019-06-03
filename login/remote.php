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
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css<?php echo "?$hash" ?>" />
</head>
<body>
    <form action="remote.php" method="POST" autocomplete="off">
        <input type="hidden" name="action" value="sql">
        <textarea name="sql" cols="50" rows="5" placeholder="SQL query"></textarea><br>
        <input type="submit" value="Submit Query">
    </form>
    
<?php
    require_once "../footer.php";  
    exec('git rev-parse --verify HEAD', $output);
    echo "
    <div class=\"right bottom\">
        <form action=\"remote.php\" method=\"POST\" autocomplete=\"off\">
            <input type=\"hidden\" name=\"action\" value=\"u\">
            <input type=\"submit\" value=\"Update\">
        </form>
        <span><a href=\"https://github.com/martinza99/dazonia/commit/$output[0]\" target=\"_top\"><b>".substr($output[0],0,6)."</b>".substr($output[0],6)."</a></span>
    </div>";
?>

</body>
</html>