<?php
    session_start();
    require_once "../login/sql.php";
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        header("Location: $domain/login");
        die();
    }
    $userId = $_SESSION["userId"];

    if(isset($_POST["action"])){
        if($userId>1)
            die("Only Admins can change tags");
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
                header("Location: .");
                break;
        }
    }

    require_once "../header.php";
?>
    <title>Tag List</title>
    <script src="tags.js"></script>
</head>
<body>
    <form action="index.php" method="post" enctype="multipart/form-data" hidden>
        <input type="file" name="file" class="fileUp" accept=".png,.jpg,.gif">
        <input type="text" name="tagName" class="tagNameInput">
        <input type="text" name="action" value="img">
    </form>
<?php
    $sql = $conn->prepare("SELECT * FROM tags ORDER BY name");
    $sql->execute();
    $result = $sql->get_result();


    echo "<div class=\"potato\">";
    while($rows = $result->fetch_assoc()){
        echo "<a href=\"$domain/list/?q=tag%3A$rows[name]\" target=\"_top\">";//open link
        echo "<div class=\"pics\" id=\"$rows[name]\">";//open table cell
        echo "<img class=\"thumb";//print thumbnail
        if($userId<2)//add click listener if admin
            echo " thumbScript";
        echo "\" src=\"img/$rows[id].png\">";//print thumbnail
        echo "<br><span class=\"tagName";//print name
        if($userId<2)//add click listener if admin
            echo " nameScript";
        echo "\">$rows[name]</span>";//print name
        echo "</div></a>";//close link and table cell
    }
    echo "</div>";
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
