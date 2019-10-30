<?php

    exec('git rev-parse --verify HEAD', $output);//requires git
    $hash = substr($output[0],0,6);
    
    function checkLogin($userID){
        $conn = $GLOBALS["conn"];
        $sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $sql->bind_param("i",$userID);
        $sql->execute();
        if($sql->get_result()->num_rows==0){
            session_destroy();
            return false;
        }
        return true;
    }

    function prePrint($var){
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    function printDatalistTags(){
        $conn = $GLOBALS["conn"];
        echo '<datalist id="tagList">';
        $sql = $conn->prepare("SELECT name FROM tags");
        $sql->execute();
        $result = $sql->get_result();
        while($rows = $result->fetch_assoc()){
            echo "<option value=\"$rows[name]\">";
        }
        echo '</datalist>';
    }
?>