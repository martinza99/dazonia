<?php
    session_start();
    require_once "login/sql.php";
    if(!isset($_SESSION["userId"])){
        header("Location: localhost/login");
        die();
    }
    $userId = $_SESSION["userId"];
    if(isset($_GET["u"]))
        $userU = htmlspecialchars($_GET["u"]);
    $filter="";
    if(isset($_GET["q"])){
        $filter = htmlspecialchars($_GET["q"]);
    }
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
    require_once "header.php";
?>
    <title>sideView</title>
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
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
    $result =  $sql->get_result();
    $conn->close();

    echo "<div class=\"potato\">";
    while($rows = $result->fetch_assoc()){
        echo "<div class=\"pics\" id=\"$rows[name]\">";//open table cell
        echo "<a href=\"$domain/view/?id=$rows[name]\" target=\"_top\">";//open link
        echo "<img src=\"../thumbnails/$rows[name]\" alt=\"$rows[name]\">";//print thumbnail
        echo "</a></div>";//close link and table cell
    }
    echo '<a href="?p='.($p-1).'" target="_top"><button>←</button></a><a href="?p='.($p+1).'" target="_top"><button>→</button></a>';
    echo "</div>";
?>
</body>
</html>

