<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("../include/head.php"); ?>
	<title>Dazonia Home</title>
	<link rel="stylesheet" href="/static/home/home.css">
</head>

<body>
	<?php include("../include/nav.php"); ?>
	<main class="potato">
		<?php while ($file = $sql->fetch()) :
			list($width, $height) = getimagesize($UPLOADS . "/thumbnails/$file->filename");
		?>
			<a href="<?= $CDN ?>/files/<?= $file->filename ?>?q=<?= $q ?>">
				<div class="pics picsBorder" id="<?= $file->filename ?>">
					<?php if (substr($file->filename, -4) == ".gif") : ?>
						<button class="thumbButton sideView">►</button>
					<?php endif;
					if ($file->avgrating) : ?>
						<img class="starView" src="/static/list/img/<?= $file->avgrating ?>.png" alt="<?= $file->avgrating ?>">
					<?php endif; ?>
					<img class="thumb" src="<?= $CDN ?>/thumbnails/<?= $file->filename ?>" alt="<?= $file->filename ?>" loading="lazy" width="<?= $width ?>" height="<?= $height ?>">
				</div>
			</a>
		<?php endwhile; ?>
	</main>
	<aside class="pageButtons">
		<a href="/?p=<?= ($p - 1) ?>&q=<?= $q ?>"><button>←</button></a>
		<span><?= $p ?></span>
		<a href="/?p=<?= ($p + 1) ?>&q=<?= $q ?>"><button>→</button></a>
	</aside>
</body>

</html>