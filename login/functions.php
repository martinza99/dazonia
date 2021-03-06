<?php

$user;
if (isset($_SESSION["userId"])) {
	$sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
	$sql->bind_param("i", $_SESSION["userId"]);
	$sql->execute();
	$result = $sql->get_result();
	$user = $result->fetch_object();
	if ($result->num_rows == 0) {
		session_destroy();
		return false;
	}
}

function prePrint($var)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

function checkLogin()
{
	if (!isset($GLOBALS["user"])) {
		http_response_code(401);
		die('401 Unauthorized<br>Not logged in!<br><a href="/login?fw=' . $_SERVER["REQUEST_URI"] . '">Go to Login</a>');
	}
}

function checkAdmin()
{
	checkLogin();
	if (!$GLOBALS["user"]->isAdmin) {
		http_response_code(403);
		die('403 Forbidden<br>Admin only page!<br><a href="/login?fw=' . $_SERVER["REQUEST_URI"] . '">Go to Login</a>');
	}
}

function printDatalistTags()
{
	$conn = $GLOBALS["conn"];
	$sql = $conn->prepare("SELECT tags.name AS 'tagName', COUNT(*) AS count FROM tags LEFT JOIN tagfile ON tags.id = tagfile.tagId GROUP BY tags.id ORDER BY COUNT DESC");
	$sql->execute();
	$result = $sql->get_result();
	echo '<datalist id="tagList">';
	while ($rows = $result->fetch_object()) {
		echo "<option value=\"$rows->tagName\">";
	}
	echo '</datalist>';
}
