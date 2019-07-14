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
    $random = "";
    $slide = 0;
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
    if($fileId==NULL)
        die("ID not found");

    if(isset($_GET["random"])){
        $sql = $conn->prepare("SELECT * FROM `files` ORDER BY rand() LIMIT 1");
    }
    #region sql
    $paramValues = array();
    $paramType = "";
    
    array_push($paramValues,$fileId);
    $paramType .= "i";

    $searchTag = filterSearch("tag:");
    if($searchTag!=""){
        array_push($paramValues,$searchTag);
        $paramType .= "s";
    }
    $searchFile = filterSearch("file:");
    if($searchFile!=""){
        array_push($paramValues,$searchFile);
        $paramType .= "s";
    }

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

    array_push($paramValues,$fileId);
    $paramType .= "i";

    if($searchTag!=""){
        array_push($paramValues,$searchTag);
        $paramType .= "s";
    }
    if($searchFile!=""){
        array_push($paramValues,$searchFile);
        $paramType .= "s";
    }

    if($searchUser!=""){
        array_push($paramValues,intval($searchUser));
        $paramType .= "i";
    }
    if($searchRating>0){
        array_push($paramValues,intval($searchRating));
        $paramType .= "i";
    }
    if($filter!=""){
        array_push($paramValues,$filter);
        $paramType .= "s";
    }
    $sql = "
        SELECT * FROM(
            SELECT * FROM(
                SELECT
                    files.id AS fileID,
                    files.name AS fileName,
                    files.ogName AS fileOgName,
                    files.userId AS fileUserId,
                    users.name AS username,
                    AVG(userrating.rating) AS avgrating
                FROM
                    files
                LEFT JOIN users ON users.id = files.userId
                LEFT JOIN userrating ON userrating.fileId = files.id
                LEFT JOIN tagFile ON tagfile.fileId = files.id
                LEFT JOIN tags ON tags.id = tagfile.tagId
                WHERE
                    files.id >= ? ";//current ID >= files.id - left + mid
                if($searchTag!="")
                    $sql.="AND tags.name = ? ";//filter tags
                if($searchFile!="")
                    $sql.="AND files.name LIKE concat('%',?,'%') ";//filter name
                if($searchUser!="")
                    $sql.="AND users.id = ? ";//filter upload user
                $sql .= "GROUP BY files.id
                ORDER BY files.id ASC
                LIMIT 2 -- prev current
            ) AS innerPrevCurr
        WHERE
            0 = 0 "; // placeholder WHERE in case no filter
        if($searchRating > 0)
            $sql .= "AND avgrating >= ? ";//min rating search
        if($searchRating === "0")
            $sql .= "AND avgrating IS NULL ";//unrated search
        if($filter!="")
            $sql .= "AND fileOgName LIKE concat('%',?,'%') ";//filter title
        $sql .= "
        ORDER BY innerPrevCurr.fileID DESC
        LIMIT 2 -- SQL bug
        )AS prevCurr
    UNION
    SELECT * FROM(
        SELECT * FROM(
            SELECT
                files.id AS fileID,
                files.name AS fileName,
                files.ogName AS fileOgName,
                files.userId AS fileUserId,
                users.name AS username,
                AVG(userrating.rating) AS avgrating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.id
            LEFT JOIN tagFile ON tagfile.fileId = files.id
            LEFT JOIN tags ON tags.id = tagfile.tagId
            WHERE ";
            if(!isset($_GET["random"]))
                $sql .= "files.id < ? ";//current ID <= files.id - curr + right
            else 
                $sql .= "files.id <> ? ";
            if($searchTag!="")
                $sql.="AND tags.name = ? ";//filter tags
            if($searchFile!="")
                $sql.="AND files.name LIKE concat('%',?,'%') ";//filter name
            if($searchUser!="")
                $sql.="AND users.id = ? ";//filter upload user
            $sql .= "GROUP BY files.id ORDER BY ";
            if(!isset($_GET["random"]))
                $sql .= "files.id DESC ";
            else
                $sql .= "RAND() ";
            $sql .= "
        ) AS innerNext

        WHERE
            0 = 0 "; // placeholder WHERE in case no filter
        if($searchRating > 0)
            $sql .= "AND avgrating <= ? ";//min rating search
        if($searchRating === "0")
            $sql .= "AND avgrating IS NULL ";//unrated search
        if($filter!="")
            $sql .= "AND fileOgName LIKE concat('%',?,'%') ";//filter title
        $sql .= "
            LIMIT 1 -- only next
        )AS next";
    #endregion sql
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

    $left;
    $right;
    $username;
    $userId;
    $rating;
    if($rows = $result->fetch_assoc()){
        if($rows["fileID"]>$fileId){
            $left = $rows["fileName"];
            $rows = $result->fetch_assoc();
        }
        $username = $rows["username"];
        $rating = $rows["avgrating"];
        $userId = $rows["fileUserId"];
        $username = $rows["username"];
        if($rows = $result->fetch_assoc())
            $right = $rows["fileName"];
    }else{
        die("No result");
    }
    if($username==null)
        $username = "deleted user[$userId]";
    if(!isset($_SESSION["userId"])){
        $left = $fileName;
        $right = $fileName;
    }

    require_once "../header.php";

    ?>
    <title><?php echo $fileName ?></title>
    <link rel="stylesheet" type="text/css" media="screen" href="view.css<?php echo "?$hash" ?>" />
    <script src="view.js<?php echo "?$hash" ?>"></script>
</head>

<body onkeydown="keyDown(event);">
    <?php
    if($q!="")
        $q = "&q=".urlencode($q);

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
        <div id=\"picDiv\" class=\"center\">";
        if(isset($left))
            echo "<a href=\"$domain/view/?id=$left$q\" target=\"_top\"><img id=\"prev\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>";
        if(isset($right)){
        echo "<a href=\"$domain/view/?id=$right$q";
            if(isset($slide))
                echo "&slide=$slide";
            if(isset($random))
                echo "&random";
            echo "\" target=\"_top\">";
        }
        echo "<img id=\"next\" class=\"floatLink pic\" src=\"../files/$fileName\"></a>
        <img id=\"centerImage\" class=\"pic\" src=\"../files/$fileName\">
        </div>
    ";
    echo "
    <div class=\"bottom\">
        <div class=\"starContainer\">
            ".rating($rating)."
        </div>
        <a href=\"$domain\" target=\"_top\"><button>← Back</button></a>";
        if(isset($userId)){
            echo '
            <button type="button" data-toggle="collapse" data-target="#collapseRandom" aria-expanded="false" aria-controls="collapseRandom" onclick="stopSlide();">&nbsp;<span class="glyphicon glyphicon-picture"></span></button>
            <div class="collapse" id="collapseRandom" style="position:absolute; margin-top: -100px; margin-left: 48px;">
                <div class="card card-body" id="text">
                    <form action="/view" method="GET">
                    <label>Random</label> <input type="checkbox" name="random" checked="'.isset($random).'">
                    <button type="submit">Start</button><br>
                        <label>Delay</label> <input type="number" name="slide" value="'; if($slide == 0) echo "3"; else echo $slide; echo '" min="0" step="0.5" placeholder="seconds" style="width:80px;">
                        <input type="hidden" name="id" value="'.$_GET["id"].'">';
                        if(isset($q))
                            '<input type="hidden" name="q" value="'.$q.'">';
                echo'</form>
                </div>
            </div>';
        }
        echo "<span>Uploaded by: <a href=\"$domain/list?q=u%3A$userId\" target=\"_top\">$username</a></span>
    </div>";
    if($slide > 0){
        $slide *= 1000;
        echo '
        <script>let slide = setTimeout(next, '.$slide.');
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
        if(isset($_SESSION["userId"]))
            echo "<span class=\"deleteTag glyphicon glyphicon-remove\"></span>";
        echo "</div>";
    }
    // if(isset($_SESSION["userId"])&&$_SESSION["userId"]<2){//if user is admin
    if(isset($_SESSION["userId"]))
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