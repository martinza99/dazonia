<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        die('Not logged in!');
    }

    $fileName = htmlspecialchars($_POST['id']);
    $newName = htmlspecialchars($_POST['newName']);
    $userId = $_SESSION["userId"];

    $sql = $conn->prepare("SELECT id FROM files WHERE name = ?");
    $sql->bind_param("s",$fileName);
    $sql->execute();
    $fileId = mysqli_fetch_assoc($sql->get_result())["id"];
    
    if($userId<2){
        $sql = $conn->prepare("UPDATE files SET ogName = ? WHERE id = ?");
        $sql->bind_param('si', $newName,$fileId);
    }
    else{
        $sql = $conn->prepare("UPDATE files SET ogName = ? WHERE id = ? AND userId = ?");
        $sql->bind_param("sii", $newName, $fileId, $userId);
    }

    echo $newName;
    $sql->execute();
    $conn->close();
?>