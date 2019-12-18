<?php
    session_start();
    require_once "sql.php";
    require_once 'functions.php';
    checkAdmin();
    
    if(isset($_POST["action"]))
        $action = $_POST["action"];
    else
        $action = "";
    $result;

    $cwd = "temp/";
        if(isset($_POST["cwd"]) && is_dir($_POST["cwd"])){
            $cwd = realpath($_POST["cwd"])."\\";
        }

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
                $outputExec = shell_exec("cd $cwd && $sql");
                break;
            case "r":
                exec("");
        }
    }
    if(isset($_GET["showfile"])){
        $file = $_GET["showfile"];
        if(file_exists($file)){
            ini_set('memory_limit', '-1');
            header("content-type:" . mime_content_type($file));
            echo file_get_contents($file);
            die();
        }
        else{
            http_response_code(404);
            die("404 File not found<br>$file");
        }
    }
    require_once "../header.php";

?>

    <title>Remote Server Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="login.js<?php echo "?$hash" ?>"></script>
</head>
<body>
    <div style="display: flex; flex-direction:row;">
    <form action="remote.php" method="POST" autocomplete="off" class="queryForm">
        <input type="hidden" name="action" class="formAction">
        <textarea id="sql" name="sql" cols="50" rows="5" placeholder="query"><?php if(!empty($action))echo $sql ?></textarea><br>
        <input type="text" id= "cwd" name="cwd" placeholder="Directory" style="width:100%" value="<?php if($cwd != "temp/") echo $cwd?>"><br>
        <input type="button" value="Submit SQL" onclick="setForm('sql');">
        <input type="button" value="Run CMD" onclick="setForm('cmd');">
        <?php if($action=="sql"){
            echo '<button type="button" data-toggle="collapse" data-target="#collapseRemote" aria-expanded="false" aria-controls="collapseRemote">mysqli Object</button>';
            echo '<button type="button" onclick="copyKey(\'csv\');">CSV <i class="glyphicon glyphicon-copy"></i></button>';
        }
        ?>
    </form>
    <div>
        <?php
        echo "Files in $cwd:<br>";
            $dir = scandir($cwd);
            usort($dir, "fileComparator");
            foreach ($dir as $key => $child) {
                if(is_dir("$cwd$child"))
                    echo "<a href=\"#\" class=\"postLink\"><mark>$child</mark></a><br>";
                else
                    echo "<a href=\"?showfile=$cwd$child\" target=\"_blank\">$child</a><br>";
            }
        ?>
        </div>
    </div>
    
    <div class="result" style="color:black;">
        <?php
            if($action=="cmd"){
                echo "<pre>";
                echo htmlspecialchars($outputExec);
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
                if(gettype($result)!="boolean"&&$result->num_rows>0){
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
                    echo '<pre class="csv" style=" display:none; ">'.$csv.'</pre>';
                }
            }
        ?>
    </div>
<?php
    require_once "../footer.php"; 

    function fileComparator($a, $b)
    {
        $a = $GLOBALS["cwd"].$a;
        $b = $GLOBALS["cwd"].$b;
        if(is_dir($a) && !is_dir($b)){
            return -1;
        }
        else if(!is_dir($a) && is_dir($b)){
            return 1;
        }
        return strnatcmp($a, $b);
    }

?>

</body>
</html>