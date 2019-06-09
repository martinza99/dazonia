<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    if(isset($_GET["id"]))
        $fileName = htmlspecialchars($_GET["id"]);
    else{
        header("Location: $domain/list");
        die("No id");
    }
    if(isset($_GET["slide"]))
        $slide = htmlspecialchars($_GET["slide"]);
    if(isset($_GET["random"]))
        $random = htmlspecialchars($_GET["random"]);

    $sql = $conn->prepare("SELECT id FROM files WHERE name = ?");
    $sql->bind_param("s",$fileName);
    $sql->execute();
    $fileId = mysqli_fetch_assoc($sql->get_result())["id"];

    $sql = $conn->prepare("SELECT files.*, files.userId AS fileUserId, DATE_FORMAT(created,'%d-%m-%Y') AS fCreated, users.name AS username, AVG(userrating.rating) AS avgrating FROM files LEFT JOIN users on users.id = files.userId LEFT JOIN userrating on userrating.fileId = files.id WHERE files.id = ? GROUP BY files.id");
    $sql->bind_param("i",$fileId);
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $rating = $result["avgrating"];
    $username = $result["username"];
    $userId = $result["fileUserId"];
    if($username==null)
        $username = "deleted user[$userId]";

    if(isset($_GET["random"])){
        $sql = $conn->prepare("SELECT * FROM `files` ORDER BY rand() LIMIT 1");
    }else{
    $sql = $conn->prepare("SELECT * from files WHERE id < ? ORDER BY id DESC LIMIT 1");
    $sql->bind_param("i",$fileId);
    }
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $prev = $result["name"];
    
    $sql = $conn->prepare("SELECT * from files WHERE id > ? ORDER BY id ASC LIMIT 1");
    $sql->bind_param("i",$fileId);
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $next = $result["name"];

    require_once "../header.php";
    ?>
    <title><?php echo $fileName ?></title>
    <link rel="stylesheet" type="text/css" media="screen" href="view.css<?php echo "?$hash" ?>" />
    <script src="view.js<?php echo "?$hash" ?>"></script>
</head>

<body onkeydown="keyDown(event);">
    <?php
    if(isset($_SESSION["userId"])&&$_SESSION["userId"]<2){
        echo '<div class="right top replace">';
        echo '<a href="javascript:document.querySelector(\'#fileUp\').click();">Replace Image</a>';
        echo '
        <form action="../upload.php" method="POST" enctype="multipart/form-data" autocomplete="off" id="replaceForm">
            <input id="fileUp" name="file" type="file"><br>
            <input type="hidden" value="true" name="skip">
            <input type="text" value="'.$fileName.'" name="replace">
        </form>';
        echo "</div>";
    }

    echo "<input value=\"$domain/files/$fileName\" class=\"hiddenVal\" style=\"opacity:0; height=0px;\" readonly>";
    if(isset($_GET["new"]))
        $fileName .= "?new";
    echo "
        <div id=\"picDiv\" class=\"center\">
            <a href=\"$domain/view/?id=$next\" target=\"_top\"><img id=\"prev\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>
            <a href=\"$domain/view/?id=$prev";
            if(isset($slide))
                echo "&slide=$slide";
            if(isset($random))
                echo "&random";
            echo "\" target=\"_top\"><img id=\"next\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>
            <img id=\"centerImage\" class=\"pic\" src=\"../files/$fileName\">
        </div>
    ";
    echo "
    <div class=\"bottom\">
        <div class=\"starContainer\">
            ".rating($rating)."
        </div>
        <a href=\"$domain\" target=\"_top\"><button>← Back</button></a>
        <span>Uploaded by: <a href=\"$domain/list?q=u%3A$userId\">$username</a></span>
    </div>";
    if(isset($slide)){
        echo '
        <script>setTimeout(next, '.$slide.');
        function next(){document.querySelector("#next").click(); }
        </script>';
    }
    echo '
    <div class="tagContainer bottom right">';

    $sql = $conn->prepare("SELECT tags.id AS tagID, tags.name AS tagName FROM tagfile INNER JOIN tags ON tags.id = tagfile.tagId WHERE tagFile.fileId = ? ORDER by tags.name");
    $sql->bind_param("i",$fileId);
    $sql->execute();
    if($sql === false)
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->errno . ' ' . $conn->error, E_USER_ERROR);
    $result = $sql->get_result();
    while($rows = $result->fetch_assoc()){
        echo "<div class=\"sugg\"><a href=\"$domain/list?q=tag%3A$rows[tagName]\" target=\"_top\">$rows[tagName]</a>";
        if(isset($_SESSION["userId"])&&$_SESSION["userId"]<2)
            echo "<span class=\"deleteTag glyphicon glyphicon-remove\"></span>";
        echo "</div>";
    }
    if(isset($_SESSION["userId"])&&$_SESSION["userId"]<2){
        echo'
        <input type="text" placeholder="add tag" style="width: 85%;" class="tagInput">
        <button class="ogButton">+</button>
        ';
    }
?>
</div>
</body>
</html>


<?php
function rating($rating){
    if($rating=="")
        $rating = 0;
    if($rating - floor($rating) == 0.5)
        $rating = floor($rating);
    $rating = (int) $rating;// 5.000 -> 5
    return '<img class="star" src="../list/img/'.$rating.'.png">';
}
?>