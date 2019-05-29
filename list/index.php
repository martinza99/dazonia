<?php
    session_start();
    require_once "../login/sql.php";
    if(!isset($_SESSION["userId"])){
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
    //if(isset($_GET["debug"]))
        var_dump($paramValues);
    array_push($paramValues,$lowerLimit);
    array_push($paramValues,$upperLimit);
    $paramType .= "ii";
    $sql = "SELECT * FROM (
                SELECT
                    files.id AS fileID,
                    files.name AS fileIdName,
                    files.ogName AS fileOgName,
                    files.userId AS fileUserId,
                    DATE_FORMAT(files.created,'%d-%m-%Y') AS fCreated,
                    users.name AS username,
                    ROUND(AVG(userrating.rating),0) AS avgrating
                FROM files
                LEFT JOIN users ON users.id = files.userId
                LEFT JOIN userrating ON userrating.fileId = files.name
                WHERE files.name = files.name ";
                if($searchFile!="")
                $sql.="AND files.name LIKE concat('%',?,'%') "; 
                if($searchUser!="")
                    $sql.="AND users.id = ? "; 
        $sql.="GROUP BY files.id
            ) AS subtable
            WHERE fileOgName = fileOgName ";
            if($searchRating!="")
            $sql .= "AND avgrating >= ? ";
            if($filter!="")
                $sql .= "AND fileOgName LIKE concat('%',?,'%') ";
            $sql .= "ORDER BY fileID DESC
            LIMIT ?,?";
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
    echo "<th><button class=\"deleteAllButton\">X</button></th></tr>";
    while($rows = $result->fetch_assoc()){
        echo "<tr id=\"$rows[fileIdName]\">";
        echo "<td><a href=\"$domain/view/?id=$rows[fileIdName]\" target=\"_top\"><div class=\"picsList\">";
            if(substr($rows["fileIdName"],-4)==".gif")
            echo '<button class="thumbButton listView">►</button>';
        echo "<img class=\"thumb\" src=\"../thumbnails/$rows[fileIdName]\" alt=\"$rows[fileIdName]\">";//print thumbnail
        echo "</div></a></td><td>";
        echo "<div class=\"starContainer\">";
        echo rating($rows["avgrating"]);
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
        echo "<td><button class=\"deleteButton\">X</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    if($q!="")
        $q = "&q=".$q;
    echo '<div class="navButtons"><a href="'.$domain.'/list?p='.($p-1).$q.'" target="_top"><button>←</button></a><span> '.$p.' </span><a href="'.$domain.'/list?p='.($p+1).$q.'" target="_top"><button>→</button></a></div>';
    echo '</div>';

function rating($i){
    if($i=="")
        $i = 0;
    return '<img class="star" src="img/'.$i.'.png">';
    /*
    switch($i){
        case 0: return  '<img class="star" src="img/gray.png">';
        case 1: return  '<img class="star" src="img/redGray.png">';
        case 2: return  '<img class="star" src="img/red.png">';
        case 3: return  '<img class="star" src="img/orangeGray.png">';
        case 4: return  '<img class="star" src="img/orange.png">';
        case 5: return  '<img class="star" src="img/greenGray.png">';
        case 6: return  '<img class="star" src="img/green.png">';
        case 7: return  '<img class="star" src="img/blueGray.png">';
        case 8: return  '<img class="star" src="img/blue.png">';
        case 9: return  '<img class="star" src="img/purpleGray.png">';
        case 10: return '<img class="star" src="img/purple.png">';
    }*/
    /*  print 5 stars
    $xf = '<img class="star" src="img/gray.png">';
    $rf = '<img class="star" src="img/red.png">';
    $of = '<img class="star" src="img/orange.png">';
    $gf = '<img class="star" src="img/green.png">';
    $bf = '<img class="star" src="img/blue.png">';
    $pf = '<img class="star" src="img/purple.png">';
    $rg = '<img class="star" src="img/redGray.png">';
    $og = '<img class="star" src="img/orangeGray.png">';
    $gg = '<img class="star" src="img/greenGray.png">';
    $bg = '<img class="star" src="img/blueGray.png">';
    $pg = '<img class="star" src="img/purpleGray.png">';
    switch($i){
        case 0: return  "$xf$xf$xf$xf$xf";
        case 1: return  "$rg$xf$xf$xf$xf";
        case 2: return  "$rf$xf$xf$xf$xf";
        case 3: return  "$of$og$xf$xf$xf";
        case 4: return  "$of$of$xf$xf$xf";
        case 5: return  "$gf$gf$gg$xf$xf";
        case 6: return  "$gf$gf$gf$gf$xf";
        case 7: return  "$bf$bf$bf$bg$xf";
        case 8: return  "$bf$bf$bf$bf$xf";
        case 9: return  "$pf$pf$pf$pf$pg";
        case 10: return "$pf$pf$pf$pf$pf";
    }*/
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