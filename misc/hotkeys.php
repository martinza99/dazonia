<?php
require_once(__DIR__ . '/../include/functions.php');
checkLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include(__DIR__ . "/../include/head.php") ?>
	<title>Hotkey List</title>
</head>

<body>
	<?php include(__DIR__ . "/../include/nav.php") ?>
	<h2>Tag hotkeys on /view/</h2>
	<h3>Press <kbd>Alt</kbd> + <kbd>key</kbd> to quickly tag a file</h3>
	<table style="margin: 20px;">
		<tr>
			<th>key&nbsp;</th>
			<th>tag</th>
		</tr>
		<tr>
			<td>a</td>
			<td>anime</td>
		</tr>
		<tr>
			<td>b</td>
			<td>art</td>
		</tr>
		<tr>
			<td>c</td>
			<td>tagme</td>
		</tr>
		<tr>
			<td>e</td>
			<td>ecchi</td>
		</tr>
		<tr>
			<td>g</td>
			<td>game</td>
		</tr>
		<tr>
			<td>h</td>
			<td>hentai</td>
		</tr>
		<tr>
			<td>m</td>
			<td>meme</td>
		</tr>
		<tr>
			<td>n</td>
			<td>nsfw</td>
		</tr>
		<tr>
			<td>r</td>
			<td>real</td>
		</tr>
		<tr>
			<td>s</td>
			<td>safe</td>
		</tr>
		<tr>
			<td>t</td>
			<td>text</td>
		</tr>
	</table>
	<?php include(__DIR__ . "/../include/footer.php") ?>
</body>

</html>