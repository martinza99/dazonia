<?php
    session_start();
    foreach ($_POST as $key => $value)
        $_POST[$key] = htmlspecialchars($value);
    $_POST = (object) $_POST;

    require_once '../login/sql.php';
    require_once '../login/functions.php';
    checkLogin();
    
    if(!isset($_POST->fileName) && !isset($_GET["test"])){
        http_response_code(400);
        die("400 Bad Request<br>fileName not set");
    }


    $sql = $conn->prepare("SELECT * FROM files WHERE name = ?");
    $sql->bind_param("s", $_POST->fileName);
    $sql->execute();
    $result = $sql->get_result();
    $file = $result->fetch_object();

    $response = new stdClass();
    $response->success = false;
    $response->file = $file;//->name;

    switch ($_POST->action) {
        case 'deleteFile':
            //if file belongs to user or admin
            if($user->isAdmin || $file->userId == $user->id){
                //delete from database
                $sql = $conn->prepare("DELETE files, userrating, tagfile FROM files LEFT JOIN userrating ON files.id = userrating.fileId LEFT JOIN tagfile ON files.id = tagfile.fileId WHERE files.id = ?");
                $sql->bind_param('i', $file->id);
                $sql->execute();
                //delete files
                unlink('../thumbnails/'.$file->name);
                unlink('../files/'.$file->name);

                $response->success = true;
            }
            else
                $response->error = "No permission to delete this file!";
            break;

        case 'rateFile':
            if($user->isAdmin){
                $_POST->rating = intval($_POST->rating);
                //delete if existed
                $sql = $conn->prepare("DELETE FROM userrating WHERE userID = ? AND fileId = ?");
                $sql->bind_param('ii', $user->id, $file->id);
                $sql->execute();
                
                //insert new value
                if($_POST->rating>=0 && $_POST->rating <= 10){
                    if($_POST->rating > 0){
                        $sql = $conn->prepare("INSERT INTO userrating (userID,fileId,rating) VALUES(?,?,?)");
                        $sql->bind_param('iii', $user->id, $file->id, $_POST->rating);
                        $sql->execute();
                    }
                    $sql = $conn->prepare("SELECT AVG(rating) AS avgrating FROM userrating WHERE fileId = ?");
                    $sql->bind_param("i",$file->id);
                    $sql->execute();
                    $result = $sql->get_result();
                    
                    $avgrating = $result->fetch_object()->avgrating;
                    if(!isset($avgrating))
                        $avgrating = 0;
                    else if($avgrating - floor($avgrating) == 0.5)
                        $avgrating = floor($avgrating);
                    $avgrating = (int) $avgrating;// 5.000 -> 5

                    $response->avgrating = $avgrating;
                    $response->success = true; 
                }
            }else
                $response->error = "No permission to rate files";
            break;

        case 'updateFile':
            if($user->isAdmin || $file->userId == $user->id){
                $sql = $conn->prepare("UPDATE files SET ogName = ? WHERE id = ?");
                $sql->bind_param("si", $_POST->newName, $file->id);
                $sql->execute();

                $response->success = true;
            }
            else
                $response->error = "No permission to update this file";
            break;

        case 'addTag':
        case 'deleteTag':
            if(!empty($_POST->tagName)){
                $_POST->tagName = str_replace(" ", "_", strtolower(trim($_POST->tagName)));
                $sql = $conn->prepare("SELECT * FROM tags WHERE LOWER(name) = LOWER(?)");
                $sql->bind_param('s', $_POST->tagName);
                $sql->execute();
                $result = $sql->get_result();
                $tag = $result->fetch_object();

                if($_POST->action == "addTag"){
                    if($tag==NULL){//insert new tag if it doesn't exist
                        $sql = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
                        $sql->bind_param('s', $_POST->tagName);
                        $sql->execute();
                        if(!isset($tag))
                            $tag = new stdClass();
                        $tag->id = $conn->insert_id;//get new id
                        $tag->name = $_POST->tagName;
                    }
                    
                    
                    $sql = $conn->prepare("SELECT * FROM tagfile WHERE tagId = ? AND fileId = ?");
                    $sql->bind_param('ii', $tag->id, $file->id);
                    $sql->execute();
                    $result = $sql->get_result();
                    if($result->num_rows == 0){
                        $sql = $conn->prepare("INSERT INTO tagfile (tagId,fileId) VALUES (?,?)");
                        $sql->bind_param('ii', $tag->id, $file->id);
                        $sql->execute();
                    }
                    
                    $response->tag = $tag;
                    $response->success = true;
                }
                else{
                    $sql = $conn->prepare("DELETE FROM tagfile WHERE tagId = ? AND fileId = ?");
                    $sql->bind_param('ii', $tag->id, $file->id);
                    $sql->execute();

                    $response->tag = $tag;
                    $response->success = true;
                }
            }
            else
                $response->error = "Empty tag name";
            break;
        
        default:
            $response->error = "Unknown action";
            break;
    }

    $response->action = $_POST->action;

    $conn->close();
    echo json_encode($response);
    if(!isset($_GET["test"]))
        die();
    echo "\n";
    prePrint($response);
    prePrint($_POST);
    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Action Test</title>
</head>

<body>
    <form action="action.php?test" method="post" autocomplete="off">
        <input type="hidden" name="test" value="true">
        <label for="fileName">Filename: </label><input type="text" id="fileName" name="fileName"><br>
        <label for="rating">Rating: </label><input type="text" id="rating" name="rating"><br>
        <label for="action">Action: </label><input list="actions" name="action" id="action"><br>
        <datalist id="actions">
            <option label="deleteFile" value="deleteFile" selected>deleteFile</option>
            <option label="rateFile" value="rateFile">rateFile</option>
            <option label="addTag" value="addTag">addTag</option>
            <option label="deleteTag" value="deleteTag">deleteTag</option>
            <option label="updateFile" value="updateFile">updateFile</option>
        </datalist>
        <input type="submit">
    </form>
</body>

</html>