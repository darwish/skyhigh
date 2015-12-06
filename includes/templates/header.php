<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?= !empty($pageTitle) ? "{$pageTitle} - " : "" ?>Door Crashers</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="apple-touch-icon" href="apple-touch-icon.png">

		<link rel="stylesheet" href="css/bootstrap.min.css">
		<style>
			body {
				padding-top: 50px;
				padding-bottom: 20px;
			}
			#map { height: 180px; }
		</style>
		<link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/jquery.growl.css">
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />

		<script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
		<script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.3/handlebars.js"></script>
	</head>
	<body>
		<!--[if lt IE 8]>
			<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php">Door Crashers</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<?php if (me()): ?>
					<div class="navbar-right">
						<p class="navbar-text">Welcome, <?= h(me()->username); ?></p>
						<a href="signout.php" class="navbar-btn btn btn-primary">Sign out</a>
					</div>
				<?php else: ?>
					<form method="post" action="signin.php" class="form navbar-form navbar-right" data-toggle="ajax" data-form="signin">
						<div class="form-group">
							<input type="text" name="username" placeholder="Username" class="form-control">
						</div>
						<div class="form-group">
							<input type="password" name="password" placeholder="Password" class="form-control">
						</div>
						<button type="submit" class="btn btn-success">Sign in</button>
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#registration-modal">Register</button>
					</form>
				<?php endif; ?>
			</div><!--/.navbar-collapse -->
		</div>
	</nav>

	<div class="container">