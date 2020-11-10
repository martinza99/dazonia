<?php
require_once("../include/functions.php");
checkLogin();

$p = $_GET["p"] ?? 1;
$p = intval($p);
if ($p < 1) {
	$p = 1;
}
$offset = 100 * ($p - 1);

if (isset($_GET["q"]))
	$q = htmlspecialchars($_GET["q"]);
else
	$q = "safe";

if (empty($q)) {
	$sql = $conn->prepare("SELECT file.filename, ROUND(AVG(userrating.rating)) AS avgrating FROM file NATURAL LEFT JOIN userrating GROUP BY file.fileID ORDER BY fileID DESC LIMIT 100 OFFSET :offset");
} else {
	$sql = $conn->prepare("SELECT file.filename, ROUND(AVG(userrating.rating)) AS avgrating FROM file NATURAL LEFT JOIN userrating NATURAL JOIN tagfile NATURAL JOIN tag WHERE tag.tagname = :tagname GROUP BY file.fileID ORDER BY fileID DESC LIMIT 100 OFFSET :offset");
	$sql->bindValue(":tagname", $q, PDO::PARAM_STR);
}
$sql->bindValue(":offset", $offset, PDO::PARAM_INT);
$sql->execute();

include("./template.php");
$sql = null;
