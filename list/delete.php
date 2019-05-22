<?php
    session_start();
    require_once '../login/sql.php';
    if(!isset($_SESSION["userId"])){
        die('Not logged in!');
    }

    $id = htmlspecialchars($_POST['id']);
    $userId = $_SESSION["userId"];
    
    if($userId<2){
        $sql = $conn->prepare("DELETE FROM files WHERE files.name = ?");
        $sql->bind_param('s', $id);

    }
    else{
        $sql = $conn->prepare("DELETE FROM files WHERE files.name = ? AND userId = ?");
        $sql->bind_param("si", $id, $userId);
    }

    $sql->execute();
    if($sql->affected_rows>0){//if rows got deleted
        $sql = $conn->prepare("DELETE FROM userrating WHERE fileId = ?");
        $sql->bind_param('s', $id);
        $sql->execute();
        unlink('../thumbnails/'.$id);
        unlink('../files/'.$id);
    }
    $conn->close();
?>