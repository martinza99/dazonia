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
    $sql = $conn->prepare("SELECT * FROM tags ORDER BY name");
    $sql->execute();
    $result = $sql->get_result();

    echo "<div class=\"potato\">";
    while($rows = $result->fetch_assoc()){
        echo "<a href=\"$domain/list/?q=tag%3A$rows[name]\" target=\"_top\">";//open link
        echo "<div class=\"pics\" id=\"$rows[name]\">";//open table cell
        echo "<img class=\"thumb\" src=\"img/$rows[id].png\">";//print thumbnail
        echo "<br><span class=\"tagName\">$rows[name]</span>";//print name
        echo "</div></a>";//close link and table cell
    }
    echo "</div>";
?>
</body>
</html>
