<?php
    session_start();
    require_once "../login/sql.php";
    require_once '../login/functions.php';
    if($_SESSION["userId"]>1||!checkLogin($_SESSION["userId"])){
        header("Location: ../login/");
        die();
    }
    $userId = $_SESSION["userId"];

    if(isset($_POST["action"])){
        $tagName = $_POST["tagName"];
        $sql = $conn->prepare("SELECT id FROM tags WHERE name = ?");
        $sql->bind_param("s",$tagName);
        $sql->execute();
        $tagId = mysqli_fetch_assoc($sql->get_result())["id"];
        if($sql->affected_rows==0)
            die("Tag doesn't exist");

        switch ($_POST["action"]){
            case "name": 
                $newName = $_POST["newName"];
                $sql = $conn->prepare("SELECT * FROM tags WHERE name = ?");//check if name already exists
                $sql->bind_param('s', $newName);
                $sql->execute();
                if($sql->get_result()->num_rows!=0)
                    die("Tag already exists");
                $sql = $conn->prepare("UPDATE tags SET name = ? WHERE id = ?");
                $sql->bind_param('si', $newName,$tagId);
                $sql->execute();
                die("Tag updated");
                break;
            case "img":
                $temp_name = $_FILES['file']['tmp_name'];
                resize(180,'img/'.$tagId.".png", $temp_name);
                header("Location: editor.php");
                break;
            case "delete":
                $sql = $conn->prepare("DELETE tags, tagfile FROM tags LEFT JOIN tagFile ON tagFile.tagid = tags.id WHERE tags.id = ?");
                $sql->bind_param('i', $tagId);
                $sql->execute();
                unlink("img/$tagId.png");
                break;   
        }
    }

    require_once "../header.php";
?>

    <title>Tag Editor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="tags.js"></script>
</head>
<body>
<form action="editor.php" method="post" enctype="multipart/form-data" hidden>
    <input type="file" name="file" class="fileUp" accept=".png,.jpg,.gif">
    <input type="text" name="tagName" class="tagNameInput">
    <input type="text" name="action" value="img">
</form>
<?php
    $orderBy = "tagsid";
    if(isset($_GET["order"]))
        $orderBy = $_GET["order"];
    if($orderBy=="count")
        $orderBy = "amount";

    $orderDir = "ASC";
    if(isset($_GET["dir"]))
        $orderDir = $_GET["dir"];

    $allowed_order_by  = array('tagsid', 'tagname', 'amount');
    $allowed_order_dir = array('ASC', 'DESC');
    if (!in_array(strtolower($orderBy), $allowed_order_by)||!in_array(strtoupper($orderDir), $allowed_order_dir))
        die("not allowed");
    $sql = $conn->prepare("SELECT tags.id AS tagsid, tags.name AS tagname, COUNT(tags.id) AS amount, tagFile.fileId AS fileid FROM `tags` LEFT JOIN tagFile ON tagfile.tagId = tags.id GROUP BY tags.Id ORDER BY $orderBy $orderDir, fileId $orderDir");
    $sql->execute();
    $result = $sql->get_result();
    echo '<table border="1">';
    echo '<th>Image</th>';

    echo "<th><a href=\"$domain/tags/editor.php?order=tagsid";
    if($orderBy == "tagsid" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\">ID</a></th>";

    echo "<th><a href=\"$domain/tags/editor.php?order=tagname";
    if($orderBy == "tagname" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\">Name</a></th>";

    echo "<th><a href=\"$domain/tags/editor.php?order=count";
    if($orderBy == "amount" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\">Count</a></th>";
    echo '<th><button class="deleteAllButton">X</button></th></th>';
    while($rows = $result->fetch_assoc()){
            echo "<tr id=\"$rows[tagname]\">";
            echo "<td><img class=\"thumbScript\" src=\"img/$rows[tagsid].png\"></td>";
            echo "<td>$rows[tagsid]</td>";
            echo "<td class=\"nameScript\"><a href=\"$domain/list/?q=tag%3A$rows[tagname]\">$rows[tagname]</a></td>";//print token
            if($rows["fileid"] == null)
                $rows["amount"] = 0;
            echo "<td>$rows[amount]</td>";//print count
            echo "<td><button class=\"deleteButton\">X</button></td>";
            echo "</tr>";
    }
    echo "</table>";
?>
</body>
</html>

<?php
function resize($factor, $targetFile, $originalFile) {

    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
            case 'image/jpeg':
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                    break;

            case 'image/png':
                    $image_create_func = 'imagecreatefrompng';
                    $image_save_func = 'imagepng';
                    $new_image_ext = 'png';
                    break;

            case 'image/gif':
                    $image_create_func = 'imagecreatefromgif';
                    $image_save_func = 'imagegif';
                    $new_image_ext = 'gif';
                    break;

            default: 
                die('Unknown image type');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);
    $ratio = $height / $width;

    if($ratio>1){
        $newHeight = $factor;
        $newWidth = $factor * ($width/$height);
    }
    else{
        $newWidth = $factor;
        $newHeight = $factor * ($ratio);
    }

    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($tmp, false);
    imagesavealpha($tmp,true);
    $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
    imagefilledrectangle($tmp, 0, 0, $newWidth, $newHeight, $transparent);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $image_save_func($tmp, $targetFile);
    return true;
}
?>