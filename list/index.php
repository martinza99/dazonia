<?php
require_once '../include/functions.php';
checkLogin();

$p = $_GET["p"] ?? 1;
$p = intval($p);
if ($p < 1) {
    $p = 1;
}
$offset = 100 * ($p - 1);

$q = $_GET["q"] ?? "";
$q = htmlspecialchars($q);

$bgPics = array_diff(scandir("$UPLOADS/bg/"), [".", ".."]);
if (sizeof($bgPics) > 0)
    $bg = $bgPics[array_rand($bgPics)];

$sql = $conn->prepare("SELECT ROUND(AVG(userrating.rating)) AS avgrating, file.filename, file.ogName, user.username, file.created FROM file NATURAL LEFT JOIN userrating NATURAL LEFT JOIN user GROUP BY file.fileID ORDER BY fileID DESC LIMIT 100 OFFSET :offset");
// $sql->bindValue(":tagname", $q, PDO::PARAM_STR);
$sql->bindValue(":offset", $offset, PDO::PARAM_INT);
$sql->execute();

include("template.php");
$sql = null;
