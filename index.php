<?php
    session_start();
    require_once "login/sql.php";
    require_once 'login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
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
    require_once "header.php";
?>
    <title>sideView</title>
</head>
<body>
<?php
    if(substr($filter,0,5)=="file:"){//search by filename
        $q = substr($filter,5);
        $sql = $conn->prepare("SELECT files.*, users.name AS username, AVG(userrating.rating) AS avgrating FROM files LEFT JOIN users on users.id = files.userId LEFT JOIN userrating on userrating.fileId = files.name WHERE files.name LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$q,$loweLimit,$upperLimit);
    }
    else if(substr($filter,0,2)=="r:"){//search by minimum rating
        $rating = substr($filter,1,2);
        $q = substr($filter,5);
        $sql = $conn->prepare("SELECT * FROM (SELECT files.*, users.name AS username, AVG(userrating.rating) AS avgrating FROM files LEFT JOIN users on users.id = files.userId LEFT JOIN userrating on userrating.fileId = files.name WHERE files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id) AS subtable WHERE subtable.avgrating >= ? ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sdii",$q,$rating,$loweLimit,$upperLimit);
    }
    else if(isset($userU)){//show selected user with filter
        $sql = $conn->prepare("SELECT files.*, users.name AS username, AVG(userrating.rating) AS avgrating FROM files LEFT JOIN users on users.id = files.userId LEFT JOIN userrating on userrating.fileId = files.name AND users.userId = ? WHERE files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("isii",$userU,$filter,$loweLimit,$upperLimit);
    }
    else{//show all pics with filter
        $sql = $conn->prepare("SELECT files.*, users.name AS username, AVG(userrating.rating) AS avgrating FROM files LEFT JOIN users on users.id = files.userId LEFT JOIN userrating on userrating.fileId = files.name WHERE files.ogName LIKE concat('%',?,'%') GROUP BY files.id ORDER BY id DESC LIMIT ?, ?");
        $sql->bind_param("sii",$filter,$loweLimit,$upperLimit);
    }

    $sql->execute();
    $result = $sql->get_result();
    $conn->close();


    echo "<div class=\"potato\">";
    while($rows = $result->fetch_assoc()){
        echo "<a  href=\"$domain/view/?id=$rows[name]\" target=\"_top\">";//open link
        echo "<div class=\"pics picsBorder\" id=\"$rows[name]\">";//open table cell
        if(substr($rows["name"],-4)==".gif")
            echo '<button class="thumbButton sideView">►</button>';
        echo rating($rows["avgrating"]);
        echo "<img class=\"thumb\" src=\"thumbnails/$rows[name]\" alt=\"$rows[name]\">";//print thumbnail
        echo "</div></a>";//close link and table cell
    }
    echo "</div>";
    echo '<div class="pageButtons">
        <a href="'.$domain.'/?p='.($p-1).'" target="_top"><button>←</button></a>
        <span> '.$p.' </span>
        <a href="'.$domain.'/?p='.($p+1).'" target="_top"><button>→</button></a>
    </div>';

function rating($rating){
    if($rating==""||$rating==0)
        return;
    if($rating - floor($rating) == 0.5)
        $rating = floor($rating);
    $rating = (int) $rating;// 5.000 -> 5
    return '<img class="starView" src="list/img/'.$rating.'.png">';
}
?>
</body>
</html>

