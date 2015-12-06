	<hr>

	<footer>
		<p>&copy; Door Crashers 2015</p>
	</footer>
	</div> <!-- /container -->

	<script src="js/vendor/bootstrap.min.js"></script>
	<script src="js/vendor/jquery.growl.js"></script>
	<script src="https://www.simplify.com/commerce/simplify.pay.js"></script>

	<script src="js/plugins.js"></script>
	<script src="js/main.js"></script>

	<div class="modal fade" id="registration-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Register</h4>
				</div>
				<div class="modal-body">
					<form method="post" action="register.php" class="form" data-toggle="ajax" data-form="register">
						<div class="form-group">
							<label for="username" class="control-label">Username</label>
							<input type="text" class="form-control" name="username" id="username" autofocus>
						</div>

						<div class="form-group">
							<label for="password" class="control-label">Password</label>
							<input type="password" class="form-control" name="password" id="password">
						</div>

						<button class="btn btn-primary">Register</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Sign In or Register</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<h4>Sign In</h4>
							<form method="post" action="signin.php" class="form" data-toggle="ajax" data-form="signin">
								<input type="hidden" name="pay" value="1">
								<div class="form-group">
									<input type="text" name="username" placeholder="Username" class="form-control" autofocus>
								</div>
								<div class="form-group">
									<input type="password" name="password" placeholder="Password" class="form-control">
								</div>
								<button type="submit" class="btn btn-success">Sign in</button>
							</form>
						</div>
						<div class="col-sm-6">
							<h4>Register</h4>
							<form method="post" action="register.php" class="form" data-toggle="ajax" data-form="register">
								<input type="hidden" name="pay" value="1">
								<div class="form-group">
									<input type="text" name="username" placeholder="Username" class="form-control">
								</div>
								<div class="form-group">
									<input type="password" name="password" placeholder="Password" class="form-control">
								</div>
								<button type="submit" class="btn btn-primary">Register</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="loading-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm" role="document" style="top:50%; position:fixed; left:50%; margin-top:-26px; margin-left:-150px;">
			<div class="modal-content">
				<div class="modal-body text-center">
					Loading...
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="store-location-modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document" style="position:fixed; left:10%; width:80%; height:90%">
			<iframe style="width: 100%; height: 100%" id="mapframe" src="#"></iframe>
		</div>
	</div>
	</body>
</html>
