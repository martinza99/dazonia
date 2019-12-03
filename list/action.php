<style>
    body {
        background-color: #676363;
    }
</style>
<?php
session_start();
require_once '../login/sql.php';
require_once '../login/functions.php';
if (!isset($_SESSION["userId"]) || !checkLogin($_SESSION["userId"])) {
    die('Not logged in!');
}

$fileName = "ICoeS.png"; //htmlspecialchars($_POST['id']);
$userId = $_SESSION["userId"];

$sql = $conn->prepare("SELECT * FROM files WHERE name = ?");
$sql->bind_param("s", $fileName);
$sql->execute();
$file = $sql->get_result()->fetch_object();
prePrint($file);
die();

if ($userId < 2) {
    $sql = $conn->prepare("DELETE files, userrating, tagfile FROM files LEFT JOIN userrating ON files.id = userrating.fileId LEFT JOIN tagfile ON files.id = tagfile.fileId WHERE files.id = ?");
    $sql->bind_param('i', $fileId);
} else {
    $sql = $conn->prepare("DELETE files, userrating, tagfile FROM files LEFT JOIN userrating ON files.id = userrating.fileId LEFT JOIN tagfile ON files.id = tagfile.fileId WHERE files.id = ? AND files.userId = ?");
    $sql->bind_param("ii", $fileId, $userId);
}

$sql->execute();
if ($sql->affected_rows > 0) { //if rows got deleted
    unlink('../thumbnails/' . $fileName);
    unlink('../files/' . $fileName);
    echo "deleted $sql->affected_rows rows";
}
$conn->close();
