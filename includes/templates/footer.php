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
							<input type="text" class="form-control" name="username" id="username">
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
	</body>
</html>