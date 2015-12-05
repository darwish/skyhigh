<?php require __DIR__ . '/../includes/start.php'; ?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<div class="jumbotron">
	<form action="search.php">
		<div class="row">
			<div class="form-group form-group-lg">
				<div class="col-xs-10">
					<input type="search" class="form-control" name="q" placeholder="What are you shopping for?">
				</div>

				<div class="col-xs-2">
					<button type="submit" class="btn btn-primary btn-lg">Search</button>
				</div>
			</div>
		</div>
	</form>
</div>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>