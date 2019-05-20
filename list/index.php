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
        $filter = htmlspecialchars($_GET["q"]);
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
    if(isset($userU)){//show selected user (?u=xyz)
        $sql = $conn->prepare("SELECT files.*, users.name AS username FROM files INNER JOIN users on users.id = files.userId AND userId = ? AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("isii",$userU,$filter,$loweLimit,$upperLimit);
    }
    else{// if($userId==0){//show all users (as admin)
        $sql = $conn->prepare("SELECT files.*, users.name AS username FROM files INNER JOIN users on users.id = files.userId AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC LIMIT ?, ?");
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
            echo "<td><a href=\"$domain/view/?id=$rows[name]\" target=\"_top\"><div class=\"pics\">";
             if(substr($rows["name"],-4)==".gif")
                echo '<button class="thumbButton listView">►</button>';
            echo "<img class=\"thumb\" src=\"../thumbnails/$rows[name]\" alt=\"$rows[name]\">";//print thumbnail
            echo "</div></a></td><td>";
            echo "<div class=\"starContainer\">";
            for ($i=1; $i <= 10; $i++) { 
                echo "<button class=\"starButton\">$i</button>";
            }
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
?>
</body>
</html>