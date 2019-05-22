<?php
    session_start();
    require_once "login/sql.php";
    if(!isset($_SESSION["userId"])){
        header("Location: $domain/login");
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
    <link rel="stylesheet" type="text/css" media="screen" href="main.css<?php echo "?$hash" ?>" />
</head>
<body>
<?php
    if(isset($userU)){//show selected user (?u=xyz)
        $sql = $conn->prepare("SELECT files.*, users.name AS username ,AVG(userrating.rating) AS rating FROM files LEFT JOIN users on users.id = files.userId  LEFT JOIN userrating on userrating.fileId = files.name AND userId = ? AND files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("isii",$userU,$filter,$loweLimit,$upperLimit);
    }
    else{// if($userId==0){//show all users (as admin)
        $sql = $conn->prepare("SELECT files.*, users.name AS username ,AVG(userrating.rating) AS rating FROM files LEFT JOIN users on users.id = files.userId  LEFT JOIN userrating on userrating.fileId = files.name AND files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$filter,$loweLimit,$upperLimit);
    }
    /*else{//only current user (non admin default)
        $sql = $conn->prepare("SELECT files.* FROM files  WHERE userId = '$userId' AND files.ogName LIKE concat('%',?,'%') ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$filter,$loweLimit,$upperLimit);
    }*/

    $sql->execute();
    $result = $sql->get_result();
    $conn->close();


    echo "<div class=\"potato\">";
    while($rows = $result->fetch_assoc()){
        echo "<a  href=\"$domain/view/?id=$rows[name]\" target=\"_top\">";//open link
        echo "<div class=\"pics picsBorder\" id=\"$rows[name]\">";//open table cell
        if(substr($rows["name"],-4)==".gif")
            echo '<button class="thumbButton sideView">►</button>';
        echo rating($rows["rating"]);
        echo "<img class=\"thumb\" src=\"thumbnails/$rows[name]\" alt=\"$rows[name]\">";//print thumbnail
        echo "</div></a>";//close link and table cell
    }
    echo "</div>";
    echo '<div class="pageButtons">
        <a href="'.$domain.'/?p='.($p-1).'" target="_top"><button>←</button></a>
        <span> '.$p.' </span>
        <a href="'.$domain.'/?p='.($p+1).'" target="_top"><button>→</button></a>
    </div>';

function rating($i){
    $i = round($i);
    switch($i){
        case 1: return  '<img class="starView" src="list/img/redGray.png">';
        case 2: return  '<img class="starView" src="list/img/red.png">';
        case 3: return  '<img class="starView" src="list/img/orangeGray.png">';
        case 4: return  '<img class="starView" src="list/img/orange.png">';
        case 5: return  '<img class="starView" src="list/img/greenGray.png">';
        case 6: return  '<img class="starView" src="list/img/green.png">';
        case 7: return  '<img class="starView" src="list/img/blueGray.png">';
        case 8: return  '<img class="starView" src="list/img/blue.png">';
        case 9: return  '<img class="starView" src="list/img/purpleGray.png">';
        case 10: return '<img class="starView" src="list/img/purple.png">';
    }
}
?>
</body>
</html>

