<?php
require __DIR__ . '/../includes/start.php';
$categories = findParentCategories();
$rows = array_chunk($categories, 3);
?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<h1>Search for an Item</h1>

<?php partial("search-bar", ['q' => ""]); ?>

<h2>Browse Categories</h2>

<?php foreach ($rows as $row): ?>
	<div class="row category-row">

	<?php foreach ($row as $parent): ?>
	<div class="col-sm-4">
		<div class="category category-parent"><a href="search.php?cat=<?= $parent->id ?>"><?= h($parent->name); ?></a></div>

		<?php foreach (findChildCategories($parent) as $child): ?>
			<div class="category category-child"><a href="search.php?cat=<?= $child->id ?>"><?= h($child->name); ?></a></div>
		<?php endforeach; ?>

	</div>
	<?php endforeach; ?>

	</div>
<?php endforeach; ?>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>