<?php
    session_start();
    require_once 'login/sql.php';
    if(isset($_POST['username'])){
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        
        if(!checkUser($username,$password)){
            die('Wrong Username or Password <br><a href="./login/" target="_top">');
        }
    }
    else if(!isset($_SESSION["userId"])){
        header('Location: ./login/');
        die();
    }
    else{
        $userId = $_SESSION["userId"];
    }

    $hideLink = isset($_POST['hideLink']);

    $filename = makeName();
    //create file from actual upload (ShareX)
    $name = $_FILES["file"]["name"];
    $temp_name  = $_FILES['file']['tmp_name'];
    list($width, $height) = getimagesize($temp_name);
    $location = 'files/';

    resize(128,'./thumbnails/'.$filename, $temp_name);
    insertName($filename,$name,$userId);
    if(!move_uploaded_file($temp_name, $location.$filename))
        die('No file uploaded!');
    printLink($filename,$hideLink);

function printLink($filename,$hideLink){       
    $actual_link = $domain."/".$filename; //creates full URI
    if(!$hideLink)//enable <a> tag
        echo "<a href=\"$actual_link\" target=\"_top\">";
    echo $actual_link;
    if(!$hideLink){//closes <a> tag
        echo '</a>';
    }
}

function makeName(){
    $temp = new SplFileInfo($_FILES['file']['name']);
    $oldname = $_FILES['file']['name'];
    $filetype = $temp->getExtension();
    do{
        $filename = generateRandomString(3).'.'.$filetype;
    }while(checkName($filename,$oldname)==false);
    return $filename;
}

function generateRandomString($length){//generates random strings
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function resize($newWidth, $targetFile, $originalFile) {

    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
            case 'image/jpeg':
                    $image_create_func = 'imagecreatefromjpeg';
                    $image_save_func = 'imagejpeg';
                    $new_image_ext = 'jpg';
                    break;

            case 'image/png':
                    $image_create_func = 'imagecreatefrompng';
                    $image_save_func = 'imagepng';
                    $new_image_ext = 'png';
                    break;

            case 'image/gif':
                    $image_create_func = 'imagecreatefromgif';
                    $image_save_func = 'imagegif';
                    $new_image_ext = 'gif';
                    break;

            default: 
                    die('Unknown image type.<br><a href="./" target="_top">back</a>');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);
    $ratio = $height / $width;
    $newHeight = $ratio * $newWidth;
    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $image_save_func($tmp, $targetFile);
    return true;
}

function checkName($newName,$oldname){//checks db if name is taken
    $conn = $GLOBALS['conn'];//db connection
    $userId = $GLOBALS['userId'];
    $sql = $conn->prepare("SELECT * FROM files WHERE name = ?");
    $sql->bind_param("s",$newName);
    $sql->execute();
    $result = $sql->get_result();
    if($result->num_rows!=0)//return false if name is taken
        return false;       //else insert into database


    return true;
}

function insertName($newName,$oldname,$userId){
    $conn = $GLOBALS['conn'];//db connection
    $sql = $conn->prepare("INSERT INTO `files`(`name`, `ogName`, `userId`) VALUES (?,?,?)");
    $sql->bind_param("ssi",$newName,$oldname,$userId);
    $sql->execute();
    $conn->close();
}

function checkUser($userId,$password){
    $conn = $GLOBALS['conn'];
    $sql =  $sql = $conn->prepare("SELECT * FROM `users` WHERE `name` = ?");
    $sql->bind_param("s",$userId);
    $sql->execute();
    $result = $sql->get_result();
    $row = mysqli_fetch_assoc($result);
    if(password_verify($password, $row['password'])){
        $GLOBALS['userId'] = $row['id'];
        return true;
    }
}

?>