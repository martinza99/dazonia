<?php
    session_start();
    require_once "../login/sql.php";
    if(!isset($_SESSION["userId"])){
        header("Location: $domain/login");
        die();
    }
    $userId = $_SESSION["userId"];
    if(isset($_GET["u"]))
    $userU = htmlspecialchars($_GET["u"]);
    $filter="";
    if(isset($_GET["q"]))
        $filter = $_GET["q"];
    $p = 0;
    if(isset($_GET["p"])){
        $p = intval(htmlspecialchars($_GET["p"]));
        if($p<0)
            $p = 0;
        $loweLimit = 100 * $p;
    }else{
        $loweLimit = 0;
    }
    $upperLimit = $loweLimit + 100;
    require_once "../header.php";
?>	
    <title>File List</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../main.css<?php echo "?$hash" ?>" />
    <link rel="stylesheet" type="text/css" media="screen" href="list.css<?php echo "?$hash" ?>" />
    <script src="pics.js<?php echo "?$hash" ?>"></script>
</head>
<body>
<?php
    if(substr($filter,0,5)=="file:"){
        $filter = substr($filter,5);
        // if($userId==0){//show all users (as admin)
        $sql = $conn->prepare("SELECT files.*,users.name AS username, files.name AS rating FROM `files` INNER JOIN users on users.id = files.userId AND files.name LIKE CONCAT('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$filter,$loweLimit,$upperLimit);
    }
    else if(isset($userU)){//show selected user (?u=xyz)
        $sql = $conn->prepare("SELECT files.*, users.name AS username ,AVG(userrating.rating) AS rating FROM files INNER JOIN users on users.id = files.userId  LEFT JOIN userrating on userrating.fileId = files.name AND userId = ? AND files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("isii",$userU,$filter,$loweLimit,$upperLimit);
    }
    else{// if($userId==0){//show all users (as admin)
        $sql = $conn->prepare("SELECT files.*, users.name AS username ,AVG(userrating.rating) AS rating FROM files INNER JOIN users on users.id = files.userId  LEFT JOIN userrating on userrating.fileId = files.name AND files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$filter,$loweLimit,$upperLimit);
    }
    /*else{//only current user (non admin default)
        $sql = $conn->prepare("SELECT files.* FROM files  WHERE userId = '$userId' AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$filter,$loweLimit,$upperLimit);
    }*/
    $sql->execute();
    $result = $sql->get_result();
    $conn->close();
    echo '<div class="listTable">';
    echo '<div class="navButtons"><a href="'.$domain.'/list?p='.($p-1).'" target="_top"><button>←</button></a><span> '.$p.' </span><a href="'.$domain.'/list?p='.($p+1).'" target="_top"><button>→</button></a></div>';
    echo '<table border="1" style="margin-left: 40px; margin-top: 22px">
        <tr>
            <th><a href="'.$domain.'/" target="_top">preview</a></th>
            <th>rating</th>
            <th>fileName</th>
            <th>Title</th>';
        echo "<th>Username</th>";
    echo "<th><button class=\"deleteAllButton\">X</button></th></tr>";
    while($rows = $result->fetch_assoc()){
            echo "<tr id=\"$rows[name]\">";
            echo "<td><a href=\"$domain/view/?id=$rows[name]\" target=\"_top\"><div class=\"picsList\">";
             if(substr($rows["name"],-4)==".gif")
                echo '<button class="thumbButton listView">►</button>';
            echo "<img class=\"thumb\" src=\"../thumbnails/$rows[name]\" alt=\"$rows[name]\">";//print thumbnail
            echo "</div></a></td><td>";
            echo "<div class=\"starContainer\">";
            echo rating($rows["rating"]);
            echo "</div></td>";
            echo "<td><a href=\"$domain/files/$rows[name]\" target=\"_top\">$rows[name]</a></td>";//print filename
            echo "<td class=\"og\"><div class=\"changeName\">$rows[ogName]</div>";//print ogName
            echo "<div class=\"changeNameInput\"><input type=\"text\" value=\"$rows[ogName]\"><button class=\"updateName\">Update</button></div></td>";//print input
            echo "<td><a href=\"$domain/list?u=$rows[userId]\" target=\"_top\">$rows[username]</a></td>";
            echo "<td><button class=\"deleteButton\">X</button></td>";
            echo "</tr>";
    }
    echo "</table>";
    echo '</div>';

function rating($i){
    $i = round($i);
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
    }
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
?>
</body>
</html>