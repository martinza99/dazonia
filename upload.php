<?php
session_start();
require_once 'login/sql.php';
require_once 'login/functions.php';
$apiKey = "";
if (isset($_POST['key'])) {
    $apiKey = $_POST['key'];

    if (!checkApiKey($apiKey)) {
        http_response_code(401);
        die("401 Unauthorized<br>Wrong API-Key");
    }
} else
    checkLogin();

$replace = false;
if (isset($_POST["replace"]) && $user->isAdmin) {
    $replace = $_POST["replace"];
}

$hideLink = isset($_POST['hideLink']);
$skip = isset($_POST['skip']);

//create file from actual upload (ShareX)
$name = $_FILES["file"]["name"];
$name = substr($name, 0, strpos($name, ".", -5));
$temp_name  = $_FILES['file']['tmp_name'];

if ($replace)
    $filename = $replace;
else
    $filename = makeName();
list($width, $height) = getimagesize($temp_name);
$location = 'files/';

if ($replace) {
    checkHash();
}

resize(180, './thumbnails/' . $filename, $temp_name);

insertName($filename, $name, $user, $replace);
if (!move_uploaded_file($temp_name, $location . $filename))
    die('No file uploaded!');
printLink($filename, $apiKey);

if ($skip) {
    header("Location: /view/$filename");
}
if (!$replace && isset($webhookURL)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhookURL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "content="
        . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"] . "/view/" . $filename);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close($ch);
}

function printLink($filename, $apiKey)
{
    $url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["SERVER_NAME"];
    $actual_link = "$url/files/$filename"; //creates full URI
    if (isset($apiKey)) { //print as <a> Link
        $file = new stdClass();
        $file->url = "$url/view/$filename";
        $file->thumbnail = "$url/thumbnails/$filename";
        echo json_encode($file);
    } else
        echo "<a href=\"$actual_link\" target=\"_top\">$actual_link</a>";
}

function makeName()
{
    $temp = new SplFileInfo($_FILES['file']['name']);
    $oldname = $_FILES['file']['name'];
    $filetype = $temp->getExtension();
    $fullName = getName($GLOBALS["temp_name"]);
    $shortName = "";
    do {
        $shortName = $shortName . substr($fullName, 0, 1);
        $fullName = substr($fullName, 1);
    } while (checkName("$shortName.$filetype", $oldname) == false);
    return "$shortName.$filetype";
}

function getName($file)
{
    $md5 = md5_file($file, true);
    $b64 = base64_encode($md5);
    $b64 = str_replace(array("+", "/"), array("-", "_"), $b64);
    $u8 = utf8_decode($b64);
    return $u8;
}

function resize($resolution, $thumbname, $tempfile)
{
    exec("ffprobe -v error -select_streams v:0 -show_entries stream=height,width -of json $tempfile", $execOut);
    $size = json_decode(implode($execOut))->streams[0];
    $ratio = $size->width / $size->height;
    $sizeString =
        $ratio > 1
        ? $resolution . "x" . round($resolution / $ratio)
        : round($resolution * $ratio) . "x" . $resolution;

    exec("ffmpeg -v error -i $tempfile -vframes 1 -an -s $sizeString $thumbname.png");
    rename("$thumbname.png", $thumbname);
    return true;
}

function checkName($newName, $oldname)
{ //checks db if name is taken
    //reserved windows names
    if (preg_match("/^(CON|PRN|AUX|NUL|COM\d|LPT\d)\..*$/i", $newName))
        return false;
    $conn = $GLOBALS['conn']; //db connection
    checkHash();
    $sql = $conn->prepare("SELECT * FROM files WHERE LOWER(name) = LOWER(?)");
    $sql->bind_param("s", $newName);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows != 0) //return false if name is taken
        return false;       //else insert into database
    return true;
}

function checkHash()
{
    $conn = $GLOBALS['conn']; //db connection
    $temp_name = $GLOBALS["temp_name"];
    $version = hash_file("md5", $temp_name);
    $sql = $conn->prepare("SELECT * FROM files WHERE hash = ?");
    $sql->bind_param("s", $version);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows != 0) { //return false if name is taken
        echo "File already exists: [$version] ";
        $name = $result->fetch_object()->name;
        echo "<a href=\"$GLOBALS[url]/view/$name\">$name</a>";
        die();
    }
}

function insertName($newName, $oldname, $user, $replace)
{
    $conn = $GLOBALS['conn']; //db connection
    $temp_name = $GLOBALS["temp_name"];
    $version = hash_file("md5", $temp_name);
    if (!$replace) {
        $sql = $conn->prepare("INSERT INTO `files`(`name`, `ogName`,`hash`, `userId`) VALUES (?,?,?,?)");
        $sql->bind_param("sssi", $newName, $oldname, $version, $user->id);
    } else {
        $sql = $conn->prepare("UPDATE files SET ogName = ?, hash = ? WHERE name = ?");
        $sql->bind_param("sss", $oldname, $version, $replace);
    }
    $sql->execute();

    $fileId = $conn->insert_id;
    autoTag("gif", $newName, $fileId);
    autoTag("mp4", $newName, $fileId);
    autoTag("webm", $newName, $fileId);
    autoTag("ogg", $newName, $fileId);

    $conn->close();
}

function checkApiKey($apiKey)
{
    $conn = $GLOBALS['conn'];
    $sql =  $sql = $conn->prepare("SELECT * FROM `users` WHERE `apiKey` = ?");
    $sql->bind_param("s", $apiKey);
    $sql->execute();
    $result = $sql->get_result();
    $user = $result->fetch_object();
    if ($result->num_rows > 0) {
        $GLOBALS['user'] = $user;
        return true;
    }
}

function autoTag($tagName, $filename, $fileId)
{
    if (substr($filename, -strlen($tagName)) != $tagName)
        return;
    $conn = $GLOBALS['conn'];
    //get tag id
    $sql = $conn->prepare("SELECT * FROM tags WHERE LOWER(name) = LOWER(?)");
    $sql->bind_param('s', $tagName);
    $sql->execute();
    $result = $sql->get_result();
    $tagId = $result->fetch_object()->id;

    if ($tagId == NULL) { //insert new tag if it doesn't exist
        $sql = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
        $sql->bind_param('s', $tagName);
        $sql->execute();
        $tagId = $conn->insert_id; //get new id
    }

    $sql = $conn->prepare("SELECT *  FROM tagfile WHERE tagId = ? AND fileId = ?");
    $sql->bind_param('ii', $tagId, $fileId);
    $sql->execute();

    $result = $sql->get_result();
    $linkId = $result->fetch_object()->tagId;

    if (!isset($linkId)) {
        $sql = $conn->prepare("INSERT INTO tagfile (tagId,fileId) VALUES (?,?)");
        $sql->bind_param('ii', $tagId, $fileId);
        $sql->execute();
    }
}
