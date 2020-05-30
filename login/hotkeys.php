<?php
session_start();
require_once "sql.php";
require_once 'functions.php';
checkLogin();
$userId = $_SESSION["userId"];

require_once "../header.php";
?>

	<title>Hotkey List</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="stylesheet" href="tables.css">
	</head>

	<body>
		<h2>Tag hotkeys on /view/</h2>
		<table style="margin: 20px;">
			<tr>
				<th>key&nbsp;</th>
				<th>tag</th>
			</tr>
			<tr><td>a</td><td>anime</td></tr>
			<tr><td>b</td><td>art</td></tr>
			<tr><td>c</td><td>tagme</td></tr>
			<tr><td>e</td><td>ecchi</td></tr>
			<tr><td>g</td><td>game</td></tr>
			<tr><td>h</td><td>hentai</td></tr>
			<tr><td>n</td><td>nsfw</td></tr>
			<tr><td>r</td><td>real</td></tr>
			<tr><td>s</td><td>safe</td></tr>
			<tr><td>t</td><td>text</td></tr>
		</table>
		<?php require_once "../footer.php"; ?>
	</body>
</html>