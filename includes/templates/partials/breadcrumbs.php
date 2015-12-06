<?php if ($category): ?>
<ol class="breadcrumb">
	<li><a href="index.php">Home</a></li>

	<?php if ($category->category): ?>
	<li><a href="search.php?cat=<?= $category->category->id ?>"><?= h($category->category->name); ?></a></li>
	<?php endif; ?>

	<li><a href="search.php?cat=<?= $category->id ?>"><?= h($category->name); ?></a></li>
</ol>
<?php endif; ?>