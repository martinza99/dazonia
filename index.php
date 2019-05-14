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
    require_once "header.php";
?>
    <title>sideView</title>
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
</head>
<body>
<?php
    if(isset($userU)){//show selected user (?u=xyz)
        $sql = $conn->prepare("SELECT files.*, users.name AS username FROM files INNER JOIN users on users.id = files.userId AND userId = ? AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC ");
        $sql->bind_param("is",$userU,$filter);
    }
    else{// if($userId==0){//show all users (as admin)
        $sql = $conn->prepare("SELECT files.*, users.name AS username FROM files INNER JOIN users on users.id = files.userId AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC");
        $sql->bind_param("s",$filter);
    }
    /*else{//only current user (non admin default)
        $sql = $conn->prepare("SELECT files.* FROM files  WHERE userId = '$userId' AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC");
        $sql->bind_param("s",$filter);
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
    echo "</div>";
?>
    <div class="bottom">
        <a href="$domain" taarget="_top"><button>← Back</button></a>
    </div>
</body>
</html>

