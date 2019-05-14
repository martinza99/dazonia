<?php
    require_once '../login/sql.php';
    $id = htmlspecialchars($_GET["id"]);
    $sibling = htmlspecialchars($_GET["s"]);
    $sql = $conn->prepare("SELECT * FROM files WHERE name = ? ORDER BY id ASC");
    $sql->bind_param("s",$id);
    $sql->execute();
    $result = mysqli_fetch_assoc($sql->get_result());
    $currID = $result["id"];

    if($sibling=="p"){
        $sql = $conn->prepare("SELECT * from files WHERE id < ? ORDER BY id DESC LIMIT 1");
        $sql->bind_param("i",$currID);
    }
    
    else{
        $sql = $conn->prepare("SELECT * from files WHERE id > ? ORDER BY id ASC LIMIT 1");
        $sql->bind_param("i",$currID);
    }
    $sql->execute();
    $result = $sql->get_result();
    if($result->num_rows!=0){
    $result = mysqli_fetch_assoc($result);
        echo $result["name"];
    }
    else
        echo "none";
    $conn->close();
?>