<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">
				<img src="/static/logo.png" style="margin-top: -12px;">
			</a>
		</div>
		<div class=" navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li><a href="/list">List View</a></li>
				<li><a href="/tags">Tags</a></li>
				<li><a href="/login/users.php">Users</a></li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Mode
						<span class="caret"></span></a>
					<ul class="dropdown-menu dropdown-menu-inverse">
						<li><a href="/list?q=tag%3Asafe">Peace</a></li>
						<li><a href="/list?q=tag%3Aecchi">Ecchi</a></li>
						<li><a href="/list?q=tag%3Ahentai">Lewd</a></li>
						<li><a href="/list?q=tag%3Ansfw">Porn</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">Browse
						<span class="caret"></span></a>
					<ul class="dropdown-menu dropdown-menu-inverse">
						<li><a href="/list/?q=tag%3Aprofile_picture">Profile Pictures</a></li>
						<li><a href="/list?q=tag%3Aemote">Emotes</a></li>
						<li><a href="/list?q=tag%3Ameme">Memes</a></li>
						<li><a href="/list?q=r%3A10">Top</a></li>
					</ul>
				</li>
			</ul>
			<a href="/list/?q=" class="searchLink" hidden></a>
			<form class="navbar-form navbar-left" action="/" method="GET" autocomplete="off">
				<div class="input-group">
					<input type="search" class="form-control disableHotkeys searchInput" placeholder="Search" name="q" style="background-color: #04013c; border-color: #1e1b7b;" <?php if (isset($_GET["q"])) : ?> value="<?= htmlspecialchars($_GET["q"]); ?>" <?php endif; ?>>
					<div class="input-group-btn">
						<button class="btn btn-default" type="submit" style="height: 34px; background-color: #131a63; border-color: #1e1b7b;">
							<i class="glyphicon glyphicon-search" style="color: #c5c0c0;"></i>
						</button>
					</div>

				</div>
			</form>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="/upload"><span class="glyphicon glyphicon-heart-empty"></span> Upload</a>
				</li>
				<li>
					<a href="/misc/settings.php"><span class="glyphicon glyphicon-cog"></span></a>
				</li>
			</ul>
		</div>
	</div>
</nav>