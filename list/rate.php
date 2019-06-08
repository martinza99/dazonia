<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        die('Not logged in!');
    }

    $fileName = htmlspecialchars($_POST['id']);
    $rating = htmlspecialchars($_POST['rating']);
    $userId = $_SESSION["userId"];
    
    $sql = $conn->prepare("SELECT id FROM files WHERE name = ?");
    $sql->bind_param("s",$fileName);
    $sql->execute();
    $fileId = mysqli_fetch_assoc($sql->get_result())["id"];

    $sql = $conn->prepare("DELETE FROM userrating WHERE userID = ? AND fileId = ?");
    $sql->bind_param('ii', $userId, $fileId);
    $sql->execute();

    if($_SESSION["userId"]<2){
        //delete if existed
        $sql = $conn->prepare("DELETE FROM userrating WHERE userID = ? AND fileId = ?");
        $sql->bind_param('ii', $userId, $fileId);
        $sql->execute();

        //insert new value
        if($rating>0){
            $sql = $conn->prepare("INSERT INTO userrating (userID,fileId,rating) VALUES(?,?,?)");
            $sql->bind_param('iii', $userId, $fileId, $rating);
            $sql->execute();
        }
    }   
    
    $sql = $conn->prepare("SELECT AVG(rating) AS avgrating FROM userrating WHERE fileId = ?");
    $sql->bind_param("i",$fileId);
    $sql->execute();
    $rating = mysqli_fetch_assoc($sql->get_result())["avgrating"];
    if(!isset($rating))
        $rating = 0;
    if($rating - floor($rating) == 0.5)
        $rating = floor($rating);
    $rating = (int) $rating;// 5.000 -> 5
    echo $rating;
?>