<?php
    session_start();
    require_once '../login/sql.php';
    if(!isset($_SESSION["userId"])){
        header('Location: ../login/');
        die();
    }
    $userId = $_SESSION["userId"];
    $sql = $conn->prepare("SELECT `name` FROM `users` WHERE `id` = ?");
    $sql->bind_param("s",$userId);
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_assoc();
    $username = $rows['name'];

    $sql = $conn->prepare("SELECT COUNT(files.id) AS picCount FROM files");
    $sql->execute();
    $result = $sql->get_result();
    $rows = $result->fetch_assoc();
    $picCount = $rows["picCount"];

    require_once "../header.php";
    echo '
        <title>Dazonia</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="../main.css?'.$hash.'" />
        <script src="../main.js?'.$hash.'"></script>
    </head>
    <body>
        <div class="center">
            <form action="../upload.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                <input id="fileUp" name="file" type="file"><br>
                <input type="hidden" value="true" name="skip">
                <input type="submit">
            </form>
        </div>
        <div class="bottom">';
       if($userId==0||$userId==3){
            echo '<a href="'.$domain.'/login/token.php" target="_top">create register token</a><br>';
            echo '<a href="'.$domain.'/login/remote.php" target="_top">server settings</a><br>';
       }
		echo '<a href="'.$domain.'/list" target="_top">File-List</a><br>
            <a href="'.$domain.'/login/logout.php" target="_top"><button>Logout</button></a><span> '.$username.'</span>
        </div>';
        exec('git rev-parse --verify HEAD', $output);
        echo "<div class=\"right bottom\">
            <span><b>$picCount</b> pictures</span><br>
            <span><b>".substr($output[0],0,6)."</b>".substr($output[0],6)."</span>
        </div>";
?>
</body>
</html>
