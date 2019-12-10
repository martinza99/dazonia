<?php
    session_start();
    require_once "../login/sql.php";
    require_once '../login/functions.php';
   
    checkLogin();

    require_once "../header.php";
?>
    <title>Tag List</title>
    <script src="tags.js"></script>
</head>
<body>
<?php
    $parent = new stdClass();
    $parent->id = 0;
    if(isset($_GET["t"])){
        $parentName = $_GET["t"];

        $sql = $conn->prepare("SELECT * FROM tags WHERE name = ?");
        $sql->bind_param("s",$parentName);
        $sql->execute();
        $result = $sql->get_result();
        $parent = $result->fetch_object();
        if($sql->affected_rows==0)
            die("Parent doesn't exist");
    }
    if(isset($_GET["q"])){
        $sql = $conn->prepare("SELECT * FROM tags WHERE name LIKE concat('%',?,'%') ORDER BY name");
        $sql->bind_param("s",$_GET["q"]);
    }else{
        $sql = $conn->prepare("SELECT * FROM tags WHERE parentId = ? ORDER BY name");
        $sql->bind_param("i",$parent->id);
    }
    $sql->execute();
    $result = $sql->get_result();

    echo "
    <div class=\"right\" style=\"position:absolute; margin-right: 13px; margin-top:6px\">
        <a href=\"$domain/tags/?q=\" class=\"searchLinkTags\" target=\"_top\" hidden></a>
        <form class=\"navbar-form navbar-left\" action=\".\" autocomplete=\"off\" onsubmit=\"tagSearchFormSubmit();\">
            <div class=\"input-group\">
                <input list=\"tagList\" class=\"form-control disableHotkeys searchInputTags\" placeholder=\"Tag search\" name=\"q\" style=\"background-color: rgba(65, 65, 75, 1); border-color: #868686; display:none;\" value=\"$filter\">
                <div class=\"input-group-btn\">
                    <button class=\"btn btn-default tagSearchButton\" type=\"button\" onclick=\"showSearchBar();\" style=\"height: 34px; background-color: #56575f; border-color: rgb(134, 134, 134); border-bottom-left-radius: 4px; border-top-left-radius: 4px;\">
                    <i class=\"glyphicon glyphicon-search\" style=\"color: #c5c0c0;\"></i>
                </button>
                </div>

            </div>
        </form>
    </div>";

    printDatalistTags();

    echo "<div class=\"potato\">";
    while($rows = $result->fetch_object()){
        echo "<div class=\"pics\" id=\"$rows->name\">";//open table cell
        echo "<a href=\"$domain/list/?q=tag%3A$rows->name\" target=\"_top\">";//open list link
        $img = "img/$rows->id.png";
        if(!file_exists($img))
            $img = "img/0.png";
        echo "<img class=\"thumb\" src=\"$img\"></a>";//print thumbnail
        echo "<a href=\"$domain/tags/?t=$rows->name\" target=\"_top\">";//open tag link
        echo "<br><span class=\"tagName\">$rows->name</span></a>";//print name
        echo "</div>";// and table cell
    }
    echo "</div>";
?>
</body>
</html>
