<?php
session_start();
require_once "../login/sql.php";
require_once '../login/functions.php';

checkAdmin();

if (isset($_POST["action"])) {
    $tagName = $_POST["tagName"];
    $sql = $conn->prepare("SELECT * FROM tags WHERE name = ?");
    $sql->bind_param("s", $tagName);
    $sql->execute();
    $result = $sql->get_result();
    $tag = $result->fetch_object();
    if ($sql->affected_rows == 0) {
        if ($_POST["action"] != "new")
            die("Tag doesn't exist");
        else {
            $sql = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
            $sql->bind_param('s', $tagName);
            $sql->execute();

            die("Tag created: $tagName [$conn->insert_id]");
        }
    }

    if ($_POST["action"] == "new")
        die("Tag already exist");

    switch ($_POST["action"]) {
        case "name":
            $newName = $_POST["newName"];
            $sql = $conn->prepare("SELECT * FROM tags WHERE name = ?"); //check if name already exists
            $sql->bind_param('s', $newName);
            $sql->execute();
            if ($sql->get_result()->num_rows != 0)
                die("Tag already exists");
            $sql = $conn->prepare("UPDATE tags SET name = ? WHERE id = ?");
            $sql->bind_param('si', $newName, $tag->id);
            $sql->execute();
            die("Tag updated");
            break;
        case "img":
            $temp_name = $_FILES['file']['tmp_name'];
            resize(180, 'img/' . $tag->id . ".png", $temp_name);
            header("Location: editor.php");
            break;
        case "delete":
            //delete tag
            $sql = $conn->prepare("DELETE tags, tagfile FROM tags LEFT JOIN tagFile ON tagFile.tagid = tags.id WHERE tags.id = ?");
            $sql->bind_param('i', $tag->id);
            $sql->execute();
            unlink("img/$tag->id.png");
            //remove all parent references
            $sql = $conn->prepare("UPDATE tags SET parentId = 0 WHERE id = ?");
            $sql->bind_param('i', $tag->id);
            $sql->execute();
            die("Tag deleted");
            break;
        case "parent":
            $parentName = $_POST["newParent"];
            //get parent id
            $parentId = -1; //default id
            if ($parentName == "root")
                $parentId = 0;
            else if ($parentName != " ") {
                $sql = $conn->prepare("SELECT id FROM tags WHERE name = ?");
                $sql->bind_param("s", $parentName);
                $sql->execute();
                $result = $sql->get_result();
                $parentId = $result->fetch_object()->id;
                if ($sql->affected_rows == 0)
                    die("Parent doesn't exist");
            }
            //update parent id on selected tag
            $sql = $conn->prepare("UPDATE tags SET parentId = ? WHERE id = ?");
            $sql->bind_param('ii', $parentId, $tag->id);
            $sql->execute();
            die("Parent updated");
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
    if (isset($_GET["order"]))
        $orderBy = $_GET["order"];
    if ($orderBy == "count")
        $orderBy = "amount";
    if ($orderBy == "parent")
        $orderBy = "parentname";

    $orderDir = "ASC";
    if (isset($_GET["dir"]))
        $orderDir = $_GET["dir"];

    $allowed_order_by  = array('tagsid', 'tagname', 'parentname', 'amount');
    $allowed_order_dir = array('ASC', 'DESC');
    if (!in_array(strtolower($orderBy), $allowed_order_by) || !in_array(strtoupper($orderDir), $allowed_order_dir))
        die("not allowed");
    $sql = $conn->prepare("SELECT realTags.id AS tagsid, realTags.name AS tagname, realTags.parentId AS parentId, parentTags.name AS parentname, COUNT(realTags.id) AS amount, tagFile.fileId AS fileid FROM `tags` AS realTags LEFT JOIN tagFile ON tagfile.tagId = realTags.id LEFT JOIN tags AS parentTags ON parentTags.id = realTags.parentId GROUP BY realTags.Id ORDER BY $orderBy $orderDir, fileId $orderDir");
    $sql->execute();
    $result = $sql->get_result();
    echo '<table border="1">';
    echo '<th>Image<button class="newTagButton">New Tag</button></th>';

    echo "<th><a href=\"/tags/editor.php?order=tagsid";
    if ($orderBy == "tagsid" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\" target=\"_top\">ID</a></th>";

    echo "<th><a href=\"/tags/editor.php?order=tagname";
    if ($orderBy == "tagname" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\" target=\"_top\">Name</a></th>";

    echo "<th><a href=\"/tags/editor.php?order=parent";
    if ($orderBy == "parentname" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\" target=\"_top\">Parent</a></th>";

    echo "<th><a href=\"/tags/editor.php?order=count";
    if ($orderBy == "amount" && $orderDir == "ASC")
        echo "&dir=desc";
    echo "\" target=\"_top\">Count</a></th>";
    echo '<th><button class="deleteAllButton">X</button></th></th>';
    while ($rows = $result->fetch_object()) {
        echo "<tr id=\"$rows->tagname\">";
        $img = "img/$rows->tagsid.png";
        if (!file_exists($img))
            $img = "img/0.png";
        echo "<td><img class=\"thumbScript\" src=\"$img\"></td>";
        echo "<td><a href=\"/tags/$rows->tagname\" target=\"_top\">$rows->tagsid</a></td>";
        echo "<td class=\"nameScript\"><a href=\"/list/?q=tag%3A$rows->tagname\" target=\"_top\">$rows->tagname</a></td>"; //print tag name
        switch ($rows->parentId) {
            case -1: //no parent
                echo "<td class=\"parentScript\">(no parent)</td>"; //print no parent
                break;
            case 0: //root
                echo "<td class=\"parentScript\"><a href=\"/tags\" target=\"_top\">(root)</a></td>"; //print root
                break;
            default: //default
                echo "<td class=\"parentScript\"><a href=\"/tags/$rows->parentname\" target=\"_top\">$rows->parentname</a></td>"; //print parent name
        }
        if ($rows->fileid == null)
            $rows->amount = 0;
        echo "<td>$rows->amount</td>"; //print count
        echo "<td><button class=\"deleteButton\">X</button></td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>
</body>

</html>

<?php
function resize($factor, $targetFile, $originalFile)
{

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

    if ($ratio > 1) {
        $newHeight = $factor;
        $newWidth = $factor * ($width / $height);
    } else {
        $newWidth = $factor;
        $newHeight = $factor * ($ratio);
    }

    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($tmp, false);
    imagesavealpha($tmp, true);
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