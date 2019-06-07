<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    if($_SESSION["userId"]>1||!checkLogin($_SESSION["userId"])){
        header("Location: .");
        die();
    }    
    if(isset($_POST["action"]))
        $action = $_POST["action"];
    else
        $action = "";
    $userId = $_SESSION["userId"];
    $result;
    if(isset($_POST["action"])){
        $sql = $_POST["sql"];
        switch ($_POST["action"]){
            case "sql": 
                $result = $conn->query($sql);
                break;
            case "u":
                exec('git pull https://github.com/martinza99/dazonia.git master');
                header("Location: remote.php");
                break;
            case "cmd":
                exec($sql,$outputExec);
                $result = $outputExec[0];
                break;
            case "r":
                exec("");
        }
    }
    require_once "../header.php";

?>

    <title>Remote Server Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <form action="remote.php" method="POST" autocomplete="off" class="queryForm">
        <input type="hidden" name="action" class="formAction">
        <textarea name="sql" cols="50" rows="5" placeholder="query"><?php if(!empty($action))echo $sql ?></textarea><br>
        <input type="button" value="Submit SQL" onclick="setForm('sql');">
        <input type="button" value="Run CMD" onclick="setForm('cmd');">
    </form>
    <script>
        function setForm(_value){
            document.querySelector(".formAction").value = _value;
            document.querySelector(".queryForm").submit();
        }
    </script>
    <div class="result" style="color:black;">
       <?php
            if($action=="cmd"){
                echo "<pre>";
                print_r($outputExec);
                echo "</pre>";
            }
        else if($action=="sql"&&!$result){
            echo "<pre>";
            print_r($conn->error);
            echo "</pre>";
        }else if($action=="sql"){
                echo "<table border=\"1\">";
                $names = $result->fetch_assoc();
                    echo "<tr>";
                    foreach($names as $key => $value)
                        echo "<th>$key</th>";
                    echo "</tr><tr>";
                    foreach($names as $value)
                        echo "<td>$value</td>";
                    echo "<tr>";
                while($rows = $result->fetch_assoc()){
                    echo "<tr>";
                    foreach($rows as $value)
                        echo "<td>$value</td>";
                    echo "<tr>";
                }
                echo "</table>";
            }
        ?>
    </div>
<?php
    require_once "../footer.php"; 
?>

</body>
</html>