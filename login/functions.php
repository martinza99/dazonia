<?php

if (isset($_COOKIE["noMasking"]) && $_COOKIE["noMasking"] == "true")
	$domain = "";

exec('git rev-parse --verify HEAD', $output); //requires git
$hash = substr($output[0], 0, 6);

function checkLogin($userID)
{
	$conn = $GLOBALS["conn"];
	$sql = $conn->prepare("SELECT * FROM users WHERE id = ?");
	$sql->bind_param("i", $userID);
	$sql->execute();
	if ($sql->get_result()->num_rows == 0) {
		session_destroy();
		return false;
	}
	return true;
}

function prePrint($var)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

function printDatalistTags()
{
	if (isset($_COOKIE["showTaglist"]) && $_COOKIE["showTaglist"] == "true") {
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
}
