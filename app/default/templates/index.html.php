<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Felipo Example</title>

<?php $r->hh->addIncludes()?>

</head>
<body>

	<header class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<?= $r->hh->link('sample app', '#', array('id' => 'logo')) ?>
				<nav>
					<ul class="nav pull-right">
						<li><?= $r->hh->link('Home', '#') ?></li>
						<li><?= $r->hh->link('Help', '#') ?></li>
						<li><?= $r->hh->link('Help', '#') ?></li>
					</ul>
				</nav>
			</div>
		</div>
	</header>
	<div class="container">

		<div class="center hero-unit">
			<h1>Hello felipo</h1>
			<h2>
				This is a sample <a href="http://link.org/">Felipo</a> application
			</h2>
				<?= $r->hh->link('Sign up now!', '#', array('class' => 'btn btn-large btn-primary')) ?>
		</div>

	</div>

</body>
</html>
