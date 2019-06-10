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
            case "b":
                exec('git pull https://github.com/martinza99/dazonia.git beta');
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
        <?php if($action=="sql")
            echo '<button type="button" data-toggle="collapse" data-target="#collapseRemote" aria-expanded="false" aria-controls="collapseRemote">mysqli Object</button>';
            echo '<button type="button" onclick="copyKey(\'csv\');">CSV <i class="glyphicon glyphicon-copy"></i></button>';
        ?>
    </form>
    <script>
        function setForm(_value){
            document.querySelector(".formAction").value = _value;
            document.querySelector(".queryForm").submit();
        }

        function copyKey(_target){
            let node = document.querySelector("."+_target);
            if (document.body.createTextRange) {
                const range = document.body.createTextRange();
                range.moveToElementText(node);
                range.select();
            } else if (window.getSelection) {
                const selection = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(node);
                selection.removeAllRanges();
                selection.addRange(range);
                document.execCommand("copy");
                selection.removeAllRanges();
            } else
                console.warn("Could not select text in node: Unsupported browser.");
        }
    </script>
    <div class="result" style="color:black;">
        <?php
            if($action=="cmd"){
                echo "<pre>";
                print_r($outputExec);
                echo "</pre>";
            }
            else if($action=="sql"){
                echo'<div class="collapse" id="collapseRemote" style="position:absolute;">';
                echo '<div class="card card-body objectRemote" id="text">
                <pre>';
                print_r($conn);
                echo "</pre>
                </div>
                </div>";
                if(gettype($result)!="boolean"){
                    $csv = "";
                    $names = $result->fetch_assoc();//column names
                    echo "<table border=\"1\">";
                    echo "<tr>";
                    foreach($names as $key => $value){
                        echo "<th>$key</th>";
                    }
                    echo "</tr><tr>";
                    foreach($names as $value){
    
                        echo "<td>$value</td>";
                        $csv .= "\"$value\",";
                    }
                    $csv = rtrim($csv,',');
                    echo "<tr>";
                    while($rows = $result->fetch_assoc()){
                        echo "<tr>";
                        $csv .= "\n";
                        foreach($rows as $value){
                            echo "<td>$value</td>";
                            $csv .= "\"$value\",";
                        }
                        $csv = rtrim($csv,',');
                        echo "<tr>";
                    }
                    echo "</table>";
                    echo '<pre class="csv" style="z-index:-1000;position:absolute; opacity:0; top:0;">'.$csv.'</pre>';
                }
            }
        ?>
    </div>
<?php
    require_once "../footer.php"; 
?>

</body>
</html>