<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        die('Not logged in!');
    }
    if($_SESSION["userId"]>1)
        die("Admin only");

    $fileName = htmlspecialchars($_POST['id']);
    $tagName = trim(htmlspecialchars($_POST['tag']));

    $sql = $conn->prepare("SELECT id FROM files WHERE name = ?");
    $sql->bind_param("s",$fileName);
    $sql->execute();
    $fileId = mysqli_fetch_assoc($sql->get_result())["id"];

    if(empty($tagName)||empty($fileName))
        die("error");
    
    //get tag id
    $sql = $conn->prepare("SELECT * FROM tags WHERE LOWER(name) = LOWER(?)");
    $sql->bind_param('s', $tagName);
    $sql->execute();
    $tagId = mysqli_fetch_assoc($sql->get_result())["id"];

    if($tagId==NULL){//insert new tag if it doesn't exist
        $sql = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
        $sql->bind_param('s', $tagName);
        $sql->execute();
        $tagId = $conn->insert_id;//get new id
    }
    switch($_POST['action']){
    case 'c'://create tag
        $sql = $conn->prepare("SELECT *  FROM tagfile WHERE tagId = ? AND fileId = ?");
        $sql->bind_param('ii', $tagId, $fileId);
        $sql->execute();

        $linkId = mysqli_fetch_assoc($sql->get_result())["tagId"];

        if(!isset($linkId)){
            $sql = $conn->prepare("INSERT INTO tagfile (tagId,fileId) VALUES (?,?)");
            $sql->bind_param('ii', $tagId, $fileId);
            $sql->execute();
        }
        echo $tagId;
        break;
        
    case 'd'://delete tag
        $sql = $conn->prepare("DELETE FROM tagfile WHERE tagId = ? AND fileId = ?");
        $sql->bind_param('ii', $tagId, $fileId);
        $sql->execute();
        break;
    }
?>