<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        die('Not logged in!');
    }

    $id = htmlspecialchars($_POST['id']);
    $newName = htmlspecialchars($_POST['newName']);
    $userId = $_SESSION["userId"];
    
    if($userId<2){
        $sql = $conn->prepare("UPDATE files SET ogName = ? WHERE name = ?");
        $sql->bind_param('ss', $newName,$id);
    }
    else{
        $sql = $conn->prepare("UPDATE files SET ogName = ? WHERE name = ? AND userId = ?");
        $sql->bind_param("ssi", $newName, $id, $userId);
    }

    echo $newName;
    $sql->execute();
    $conn->close();
?>