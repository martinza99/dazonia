<?php
    session_start();
    require_once '../login/sql.php';
    if(!isset($_SESSION["userId"])){
        die('Not logged in!');
    }

    $fileid = htmlspecialchars($_POST['id']);
    $rating = htmlspecialchars($_POST['rating']);
    $userId = $_SESSION["userId"];
    
    if($_SESSION["userId"]==0||$_SESSION["userId"]==3){
        //delete if existed
        $sql = $conn->prepare("DELETE FROM userrating WHERE userID = ? AND fileId = ?");
        $sql->bind_param('is', $userId, $fileid);
        $sql->execute();

        //insert new value
        $sql = $conn->prepare("INSERT INTO userrating (userID,fileId,rating) VALUES(?,?,?)");
        $sql->bind_param('isi', $userId, $fileid, $rating);
        $sql->execute();

        echo " rating: $rating, id: $id";
    }else{
        echo "No! Only Lyren and I can vote :p";
    }    
?>