<?php
session_start();
require_once "login/sql.php";
require_once 'login/functions.php';

checkLogin($user);

$p = 1;
if (isset($_GET["p"])) {
	$p = intval(htmlspecialchars($_GET["p"]));
	if ($p < 1)
		$p = 1;
	$offset = 100 * ($p - 1);
} else {
	$offset = 0;
}
require_once "header.php";
?>
<title>sideView</title>
</head>

<body>
	<?php

	// $sql = $conn->prepare("SELECT file.*, user.username, AVG(userrating.rating) AS avgrating FROM file NATURAL JOIN user NATURAL JOIN userrating NATURAL JOIN tagfile WHERE tagfile.tagid = 11 GROUP BY file.fileID ORDER BY fileID DESC LIMIT 100 OFFSET :offset");
	$sql = $conn->prepare("SELECT file.*, user.username, AVG(userrating.rating) AS avgrating FROM file NATURAL LEFT JOIN user NATURAL LEFT JOIN userrating GROUP BY file.fileID ORDER BY fileID DESC LIMIT 100 OFFSET :offset");
	$sql->bindValue(":offset", $offset, PDO::PARAM_INT);
	$sql->execute();
	echo "<div class=\"potato\">";
	while ($file = $sql->fetchObject()) {
		echo "<a  href=\"/view/$file->filename?q=tag%3Asafe\" target=\"_top\">"; //open link
		echo "<div class=\"pics picsBorder\" id=\"$file->filename\">"; //open table cell
		if (substr($file->filename, -4) == ".gif")
			echo '<button class="thumbButton sideView">►</button>';
		echo rating($file->avgrating);

		list($width, $height) = getimagesize("thumbnails/$file->filename");

		echo "<img class=\"thumb\" src=\"thumbnails/$file->filename\" alt=\"$file->filename\" loading=\"lazy\" width=\"$width\" height=\"$height\">"; //print thumbnail
		echo "</div></a>"; //close link and table cell
	}
	echo "</div>";
	echo '<div class="pageButtons">
        <a href="/?p=' . ($p - 1) . '"><button>←</button></a>
        <span> ' . $p . ' </span>
        <a href="/?p=' . ($p + 1) . '"><button>→</button></a>
    </div>';

	function rating($rating)
	{
		if ($rating == "" || $rating == 0)
			return;
		if ($rating - floor($rating) == 0.5)
			$rating = floor($rating);
		$rating = (int) $rating; // 5.000 -> 5
		return '<img class="starView" src="list/img/' . $rating . '.png">';
	}
	?>
</body>

</html>