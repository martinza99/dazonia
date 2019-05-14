<?php
    session_start();
    require_once '../login/sql.php';
    if(!isset($_SESSION["userId"])){
        die('Not logged in!');
    }

    $id = htmlspecialchars($_POST['id']);
    $userId = $_SESSION["userId"];
    
    if($userId==0){
        $sql = $conn->prepare("DELETE FROM files WHERE name = ?");
        $sql->bind_param('s', $id);
    }
    else{
        $sql = $conn->prepare("DELETE FROM files WHERE name = ? AND userId = ?");
        $sql->bind_param("si", $id, $userId);
    }

    $sql->execute();
    $conn->close();
    if($sql->affected_rows>0){//if rows got deleted
        unlink('../thumbnails/'.$id);
        unlink('../files/'.$id);
    }
?>