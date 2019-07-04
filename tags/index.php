<?php
    session_start();
    require_once "../login/sql.php";
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        header("Location: $domain/login");
        die();
    }
    $userId = $_SESSION["userId"];

    require_once "../header.php";
?>
    <title>Tag List</title>
    <script src="tags.js"></script>
</head>
<body>
<?php
    $parentId = 0;
    if(isset($_GET["t"])){
        $parentName = $_GET["t"];

        $sql = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $sql->bind_param("s",$parentName);
        $sql->execute();
        $parentId = mysqli_fetch_assoc($sql->get_result())["id"];
        if($sql->affected_rows==0)
            die("Parent doesn't exist");
    }

    $sql = $conn->prepare("SELECT * FROM tags WHERE parentId = ? ORDER BY name");
    $sql->bind_param("i",$parentId);
    $sql->execute();
    $result = $sql->get_result();

    echo "<div class=\"potato\">";
    while($rows = $result->fetch_assoc()){
        echo "<div class=\"pics\" id=\"$rows[name]\">";//open table cell
        echo "<a href=\"$domain/list/?q=tag%3A$rows[name]\" target=\"_top\">";//open list link
        echo "<img class=\"thumb\" src=\"img/$rows[id].png\"></a>";//print thumbnail
        echo "<a href=\"$domain/tag/?t=$rows[name]\" target=\"_top\">";//open tag link
        echo "<br><span class=\"tagName\">$rows[name]</span></a>";//print name
        echo "</div>";// and table cell
    }
    echo "</div>";
?>
</body>
</html>
