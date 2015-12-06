<?php
require __DIR__ . '/../includes/start.php';

$q = getvar("q");
$category_id = (int)getvar("cat");
if (!trim($q) && !$category_id) {
	redirect("index.php");
}

$category = null;
if ($category_id) {
	$category = R::load("category", $category_id);
}

$page = (int)getvar("page", 1);

$search = search($q, $category_id, $page);

$pageTitle = "Search Results";
?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<h1>Search Results</h1>

<?php if ($category): ?>
<ol class="breadcrumb">
	<?php if ($category->category): ?>
	<li><a href="search.php?cat=<?= $category->category->id ?>"><?= h($category->category->name); ?></a></li>
	<?php endif; ?>

	<li><a href="search.php?cat=<?= $category->id ?>"><?= h($category->name); ?></a></li>
</ol>
<?php endif; ?>

<?php partial("search-bar", ['q' => $q]); ?>

<?php if ($search['count']): ?>
<div class="search-results-meta">
	<?php if ($search['count'] <= $search['pageSize']): ?>
		Showing <?= $search['count'] ?> <?= $search['count'] == 1 ? "result" : "results" ?>
	<?php else: ?>
		Showing <?= $search['start'] ?> - <?= $search['end'] ?> of <?= $search['count'] ?> results
	<?php endif; ?>
</div>

<div class="search-results"></div>
<?php else: ?>
<div class="no-search-results">
	Sorry, no results found for <em><?= h($q) ?></em>. Try searching for a more general term, or browsing by category.
</div>
<?php endif; ?>

<script type="text/plain" id="result-item-template">
<a href="item.php?id={{id}}" class="result-item clearfix">
	<div class="thumbnail item-thumbnail"><img src="{{image}}" width="200"></div>
	<div class="price">
		<div class="prices">
			<div class="discount-price">{{formatMoney discount_price}}</div>
			<div class="original-price-text">Original price: <span class="original-price">{{formatMoney original_price}}</span></div>
		</div>
		<div class="discount-percentage">
			<div class="discount-percentage-badge">{{discount_percentage}}%</div>
		</div>
	</div>
	<div class="details">
		<div class="title">{{title}}</div>
		<div class="description">{{description}}</div>
	</div>
	<div class="store-info">{{shop.name}} - {{shop.distance}}</div>
</a>
</script>

<script>
$(function() {
	var resultItemTemplate = Handlebars.compile($('#result-item-template').html());
	var results = <?= json_encode(R::exportAll($search['results'])); ?>;

	for (var i = 0; i < results.length; i++) {
		var result = results[i];
		result.discount_percentage = calculateDiscount(result.discount_price, result.original_price);

		var item = resultItemTemplate(result);
		$('.search-results').append(item);
	}
});
</script>

<?php
	require_once('map.php');
?>
<?php require __DIR__ . '/../includes/templates/footer.php'; ?>