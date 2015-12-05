<div class="jumbotron search-bar">
	<form action="search.php">
		<div class="row">
			<div class="form-group form-group-lg">
				<div class="col-sm-10">
					<input type="search" class="form-control" name="q" placeholder="What are you shopping for?" value="<?= h($q) ?>">
				</div>

				<div class="hidden-sm hidden-md hidden-lg"><p></p></div>

				<div class="col-sm-2">
					<button type="submit" class="btn btn-primary btn-lg">Search</button>
				</div>
			</div>
		</div>
	</form>
</div>