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

    $filter="";
    $q = "";
    if(isset($_GET["q"])){
        $filter = $_GET["q"];
        $q = $_GET["q"];
    }

    $sql = $conn->prepare("SELECT id FROM files WHERE name = ?");
    $sql->bind_param("s",$fileName);
    $sql->execute();
    $fileId = mysqli_fetch_assoc($sql->get_result())["id"];


    if(isset($_GET["random"])){
        $sql = $conn->prepare("SELECT * FROM `files` ORDER BY rand() LIMIT 1");
    }

    $paramValues = array();
    $paramType = "";
    filterSearch("file:");

    $searchTag = filterSearch("tag:");
    if($searchTag!=""){
        array_push($paramValues,$searchTag);
        $paramType .= "s";
    }
    array_push($paramValues,-1);
    $paramType .= "i";

    $searchUser = filterSearch("u:");
    if($searchUser!=""){
        array_push($paramValues,intval($searchUser));
        $paramType .= "i";
    }
    $searchRating = filterSearch("r:");
    if($searchRating>0){
        array_push($paramValues,intval($searchRating));
        $paramType .= "i";
    }
    while(substr($filter,0,1)==" ")
        $filter = substr($filter,1);
    while(substr($filter,-1,1)==" ")
        $filter = substr($filter,0,-1);
    if($filter!=""){
        array_push($paramValues,$filter);
        $paramType .= "s";
    }
    {$sql = "
        SELECT
            subtable.*,
            lTable.lRating,
            mTable.mRating
        FROM
            (
            SELECT
                files.id AS fileID,
                files.name AS fileName,
                files.ogName AS fileOgName,
                files.userId AS fileUserId,
                DATE_FORMAT(files.created, '%d-%m-%Y') AS fCreated,
                users.name AS username,
                AVG(userrating.rating) AS avgrating,
                tags.name AS tagname
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            LEFT JOIN tagFile ON tagfile.fileId = files.id
            LEFT JOIN tags ON tags.id = tagfile.tagId
            WHERE
                files.id > ? ";
            if($searchTag!="")
                $sql.="AND tags.name = ? "; 
            if($searchUser!="")
                $sql.="AND users.id = ? "; 
            $sql .= "GROUP BY
                files.id
        ) AS subtable
        LEFT JOIN(
            SELECT
                files.id AS lfileID,
                userrating.rating AS lRating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            WHERE
                userrating.userID = 0
        ) AS lTable
        ON
            lTable.lfileID = subtable.fileID
        LEFT JOIN(
            SELECT
                files.id AS mfileID,
                userrating.rating AS mRating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            WHERE
                userrating.userID = 1
        ) AS mTable
        ON
            mTable.mfileID = subtable.fileID
        WHERE
            fileName = fileName ";
        if($searchRating > 0)
            $sql .= "AND avgrating >= ? ";
        if($searchRating === "0")
            $sql .= "AND avgrating IS NULL ";
        if($filter!="")
            $sql .= "AND fileOgName LIKE concat('%',?,'%') ";
        $sql .= "ORDER BY
            subtable.fileID DESC";}
            $sql = $conn->prepare($sql);
    if($sql === false)
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->errno . ' ' . $conn->error, E_USER_ERROR);
    $a_params = array();
    foreach ($paramValues as $key => $value) {
        $a_params[$key] = &$paramValues[$key];
    }
    call_user_func_array(
        array($sql, 'bind_param'), 
        array_merge(array($paramType), $paramValues)
    );

    $sql->execute();
    $result = $sql->get_result();
    
    $prev;
    $next;
    $username;
    $userId;
    $rating;
    while($rows = $result->fetch_assoc()){
        if($rows["fileID"]==$fileId){
            $username = $rows["username"];
            $userId = $rows["fileUserId"];
            $rating = $rows["avgrating"];

            if($rows = $result->fetch_assoc())
                $prev = $rows["fileName"];
            break;
        }
        $next = $rows["fileName"];
    }
    if($username==null)
        $username = "deleted user[$userId]";
    if(!isset($prev))
        $prev = "";
    if(!isset($next))
        $next = "";

    require_once "../header.php";

    ?>
    <title><?php echo $fileName ?></title>
    <link rel="stylesheet" type="text/css" media="screen" href="view.css<?php echo "?$hash" ?>" />
    <script src="view.js<?php echo "?$hash" ?>"></script>
</head>

<body onkeydown="keyDown(event);">
    <?php
    if($q!="")
        $q = "&q=".$q;
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
            <a href=\"$domain/view/?id=$next$q\" target=\"_top\"><img id=\"prev\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>
            <a href=\"$domain/view/?id=$prev$q";
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
        <span>Uploaded by: <a href=\"$domain/list?q=u%3A$userId\" target=\"_top\">$username</a></span>
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
        // if(isset($_SESSION["userId"])&&$_SESSION["userId"]<2)//if user is admin
            echo "<span class=\"deleteTag glyphicon glyphicon-remove\"></span>";
        echo "</div>";
    }
    // if(isset($_SESSION["userId"])&&$_SESSION["userId"]<2){//if user is admin
        echo'
        <input type="text" placeholder="add tag" style="width: 85%;" class="tagInput disableHotkeys">
        <button class="ogButton">+</button>
        ';
    // }
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

function filterSearch($find){
    $filter = $GLOBALS["filter"];
    $filtered = "";
    $start = strpos($filter,$find);
   
   
    if(gettype($start)!="boolean"){
        $len = strpos($filter," ",$start) - $start - strlen($find);
        if($len>0){
            $filtered = substr($filter,$start+strlen($find),$len);
            $GLOBALS["filter"] = substr_replace($filter,"",$start,strlen($find)+$len+1);
        }
        else{
            $filtered = substr($filter,$start+strlen($find));
            $GLOBALS["filter"] = substr_replace($filter,"",$start);
        }
    }
    return $filtered;
}
?>