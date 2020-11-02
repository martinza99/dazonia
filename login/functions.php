<?php
require_once("sql.php");

$user;
if (isset($_SESSION["userID"])) {
	$stmt = $conn->prepare("SELECT * FROM user WHERE userID = :userID");
	$stmt->bindValue(":userID", $_SESSION["userID"], PDO::PARAM_INT);
	$stmt->execute();
	$user = $stmt->fetchObject();
	if ($user === null) {
		session_destroy();
		return false;
	}
}

function prePrint(...$args)
{
	echo "<details open><summary>";
	echo sizeof($args);
	echo " Variables</summary><hr><pre>";
	foreach ($args as $arg) {
		print_r($arg);
		echo "<hr>";
	}
	echo "</pre></details>";
}

function checkLogin($user)
{
	if (!isset($user)) {
		http_response_code(401);
		die('401 Unauthorized<br>Not logged in!<br><a href="/login?fw=' . $_SERVER["REQUEST_URI"] . '">Go to Login</a>');
	}
}

function checkAdmin($user)
{
	checkLogin($user);
	if ($user->isAdmin) {
		http_response_code(403);
		die('403 Forbidden<br>Admin only page!<br><a href="/login?fw=' . $_SERVER["REQUEST_URI"] . '">Go to Login</a>');
	}
}

function printDatalistTags($conn)
{
	// $conn = $GLOBALS["conn"];
	$stmt = $conn->prepare("SELECT tag.tagname AS 'tagname', COUNT(tag.tagname) AS count FROM tags LEFT JOIN tagfile ON tag.tagID = tagfile.tagID GROUP BY tag.tagID ORDER BY COUNT DESC");
	$stmt->execute();
	$result = $stmt->get_result();
	echo '<datalist id="tagList">';
	while ($rows = $result->fetch_object()) {
		echo "<option value=\"$rows->tagName\">";
	}
	echo '</datalist>';
}
