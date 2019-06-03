<?php
    session_start();
    require_once '../login/sql.php';
    require_once '../login/functions.php';
    if(!isset($_SESSION["userId"])||!checkLogin($_SESSION["userId"])){
        die('Not logged in!');
    }

    $fileid = htmlspecialchars($_POST['id']);
    $rating = htmlspecialchars($_POST['rating']);
    $userId = $_SESSION["userId"];
    
    if($_SESSION["userId"]<2){
        //delete if existed
        $sql = $conn->prepare("DELETE FROM userrating WHERE userID = ? AND fileId = ?");
        $sql->bind_param('is', $userId, $fileid);
        $sql->execute();

        //insert new value
        if($rating>0){
            $sql = $conn->prepare("INSERT INTO userrating (userID,fileId,rating) VALUES(?,?,?)");
            $sql->bind_param('isi', $userId, $fileid, $rating);
            $sql->execute();
        }
    }   
    
    $sql = $conn->prepare("SELECT AVG(userrating.rating) AS avgrating FROM userrating WHERE fileId = ?");
    $sql->bind_param("s",$fileid);
    $sql->execute();
    $rating = mysqli_fetch_assoc($sql->get_result())["avgrating"];
    if(!isset($rating))
        $rating = 0;
    if($rating - floor($rating) == 0.5)
        $rating = floor($rating);
    $rating = (int) $rating;// 5.000 -> 5
    echo $rating;
?>