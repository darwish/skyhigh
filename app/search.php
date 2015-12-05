<?php
require __DIR__ . '/../includes/start.php';

$q = getvar("q");
if (!trim($q)) {
	redirect("index.php");
}

$page = (int)getvar("page", 1);

$search = search($q, $page);
?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<h1>Search Results</h1>

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
	<div class="thumbnail"><img src="{{thumbnail}}"></div>
	<div class="price">
		<div class="discount-price">{{formatMoney discountPrice}}</div>
		<div class="original-price-text">Original price: <span class="original-price">{{formatMoney originalPrice}}</span></div>
		<div class="discount-percentage">{{discountPercentage}}%</div>
	</div>
	<div class="details">
		<div class="title">{{title}}</div>
		<div class="description">{{description}}</div>
	</div>
	<div class="store-info">{{store}} - {{distance}}</div>
</a>
</script>

<script>
$(function() {
	var resultItemTemplate = Handlebars.compile($('#result-item-template').html());
	var results = <?= json_encode($search['results']); ?>;

	for (var i = 0; i < results.length; i++) {
		var result = results[i];
		result.discountPercentage = calculateDiscount(result.discountPrice, result.originalPrice);

		var item = resultItemTemplate(result);
		$('.search-results').append(item);
	}
});
</script>
<?php require __DIR__ . '/../includes/templates/footer.php'; ?>