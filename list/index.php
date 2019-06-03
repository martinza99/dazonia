<?php
    session_start();
    require_once "../login/sql.php";
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        header("Location: $domain/login");
        die();
    }
    $userId = $_SESSION["userId"];
    $filter="";
    $q = "";
    if(isset($_GET["q"])){
        $filter = $_GET["q"];
        $q = $_GET["q"];
    }
    $p = 1;
    if(isset($_GET["p"]))
        $p = intval(htmlspecialchars($_GET["p"]));
    if($p<1)
        $p = 1;
    $lowerLimit = 100 * ($p-1);
    $upperLimit = $lowerLimit + 100;
    require_once "../header.php";
?>	
    <title>File List</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css<?php echo "?$hash" ?>" />
    <link rel="stylesheet" type="text/css" media="screen" href="list.css<?php echo "?$hash" ?>" />
    <script src="pics.js<?php echo "?$hash" ?>"></script>
</head>
<body>
<?php
    $paramValues = array();
    $paramType = "";
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
    $searchRating = intval(filterSearch("r:"));
    if($searchRating!=""){//int(0) == ""
        array_push($paramValues,$searchRating);
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
    if(isset($_GET["debug"]))
        var_dump($paramValues);
    array_push($paramValues,$lowerLimit);
    array_push($paramValues,$upperLimit);
    $paramType .= "ii";
    {$sql = "
        SELECT
            subtable.*,
            lTable.lRating,
            mTable.mRating
        FROM
            (
            SELECT
                files.id AS fileID,
                files.name AS fileIdName,
                files.ogName AS fileOgName,
                files.userId AS fileUserId,
                DATE_FORMAT(files.created, '%d-%m-%Y') AS fCreated,
                users.name AS username,
               AVG(userrating.rating) AS avgrating
            FROM
                files
            LEFT JOIN users ON users.id = files.userId
            LEFT JOIN userrating ON userrating.fileId = files.name
            WHERE
                files.name = files.name ";
            if($searchFile!="")
                $sql.="AND files.name LIKE concat('%',?,'%') "; 
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
            LEFT JOIN userrating ON userrating.fileId = files.name
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
            LEFT JOIN userrating ON userrating.fileId = files.name
            WHERE
                userrating.userID = 1
        ) AS mTable
        ON
            mTable.mfileID = subtable.fileID
        WHERE
            fileIdName = fileIdName ";
        if($searchRating!="")
            $sql .= "AND avgrating >= ? ";
        if($filter!="")
            $sql .= "AND fileOgName LIKE concat('%',?,'%') ";
        $sql .= "ORDER BY
            subtable.fileID
        DESC
        LIMIT ?,?";}
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
    $conn->close();

    echo '<div class="listTable">';
    if($q!="")
        $q = "&q=".$q;
    echo '<div class="navButtons"><a href="'.$domain.'/list?p='.($p-1).$q.'" target="_top"><button>←</button></a><span> '.$p.' </span><a href="'.$domain.'/list?p='.($p+1).$q.'" target="_top"><button>→</button></a></div>';
    echo '<table border="1" style="margin-left: 40px; margin-top: 22px">
        <tr>
            <th><a href="'.$domain.'/" target="_top">preview</a></th>
            <th>rating</th>
            <th>fileName</th>
            <th>Title</th>
            <th>Username</th>
            <th>Upload Date</th>';
    echo "<th>";
    echo "<button class=\"deleteAllButton\">X</button>";
    if($_SESSION["userId"]<2){
        if($_SESSION["userId"]==0)
            $temp = "l";
        else
            $temp = "m";
        echo "<script>var USERID = '$temp';</script>";
    }
    echo "</th></tr>";
    while($rows = $result->fetch_assoc()){
        echo "<tr id=\"$rows[fileIdName]\">";
        echo "<td><a href=\"$domain/view/?id=$rows[fileIdName]\" target=\"_top\"><div class=\"picsList\">";
            if(substr($rows["fileIdName"],-4)==".gif")
            echo '<button class="thumbButton listView">►</button>';
        echo "<img class=\"thumb\" src=\"../thumbnails/$rows[fileIdName]\" alt=\"$rows[fileIdName]\">";//print thumbnail
        echo "</div></a></td><td>";
        echo "<div class=\"starContainer\">";
        if($_SESSION["userId"]<2){
            echo rating($rows["lRating"],"l");
            echo rating($rows["mRating"],"m");
        }
        echo rating($rows["avgrating"],"");
        echo "</div></td>";
        echo "<td><a href=\"$domain/files/$rows[fileIdName]\" target=\"_top\">$rows[fileIdName]</a></td>";//print filename
        echo "<td class=\"og\"><div class=\"changeName\">$rows[fileOgName]</div>";//print ogName
        echo "<div class=\"changeNameInput\"><input type=\"text\" value=\"$rows[fileOgName]\"><button class=\"updateName\">Update</button></div></td>";//print input
        echo "<td>";
        if($rows["username"]==null)
        $rows["username"] = "deleted<br>user[$rows[fileUserId]]";
        echo "<a href=\"$domain/list?q=u%3A$rows[fileUserId]\" target=\"_top\">$rows[username]</a>";
        echo "</td>";
        echo "<td class=\"date\">".substr($rows["fCreated"],0,10)."</td>";
        echo "<td>";
        if($_SESSION["userId"]<2 || $_SESSION["userId"]==$rows["fileUserId"])
            echo "<button class=\"deleteButton\">X</button>";
        echo "</td></tr>";
    }
    echo "</table>";
    if($q!="")
        $q = "&q=".$q;
    echo '<div class="navButtons"><a href="'.$domain.'/list?p='.($p-1).$q.'" target="_top"><button>←</button></a><span> '.$p.' </span><a href="'.$domain.'/list?p='.($p+1).$q.'" target="_top"><button>→</button></a></div>';
    echo '</div>';

function rating($rating,$u){
    if($rating=="")
        $rating = 0;
    if($u!=""){
        $u = $u."star userStar";
    }
    else{
        $u = "star";
    }
    if($rating - floor($rating) == 0.5)
        $rating = floor($rating);
    $rating = (int) $rating;// 5.000 -> 5
    return '<img class="'.$u.'" src="img/'.$rating.'.png">';
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
</body>
</html>