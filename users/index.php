<?php

require_once("../login/sql.php");
require_once("../include/functions.php");
checkLogin($user);

if (isset($_POST["action"])) {
	switch ($_POST["action"]) {
		case "d":
			$sql = $conn->prepare("DELETE FROM user WHERE userID = :userID");
			$sql->bindValue(":userID", $_POST["id"]);
			$sql->execute();
			die("User deleted");
			break;
	}
}
$sql = $conn->prepare("SELECT * FROM user ORDER BY userID");
$sql->execute();

include("template.php");
$sql = null;

function timeElapsed($dateTime)
{
	$now = new DateTime;
	$ago = new DateTime($dateTime);
	$diff = $now->diff($ago);

	if ($diff->y > 2000)
		return "-";
	$diff->w = floor($diff->d / 7);
	$diff->d -= $diff->w * 7;

	$string = array(
		'y' => 'year',
		'm' => 'month',
		'w' => 'week',
		'd' => 'day',
		'h' => 'hour',
		'i' => 'minute',
		's' => 'second',
	);
	foreach ($string as $k => &$v) {
		if ($diff->$k) {
			$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
		} else {
			unset($string[$k]);
		}
	}

	$string = array_slice($string, 0, 1);
	return $string ? implode(', ', $string) . ' ago' : 'just now';
}
