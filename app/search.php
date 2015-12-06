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

<?php partial("breadcrumbs", ['category' => $category]); ?>

<?php partial("search-bar", ['q' => $q]); ?>

<?php if ($search['count']): ?>
<div class="search-results-meta">
	<?php if ($search['count'] <= $search['pageSize']): ?>
		Showing <?= $search['count'] ?> <?= $search['count'] == 1 ? "result" : "results" ?>
	<?php else: ?>
		Showing <?= $search['start'] ?> - <?= $search['end'] ?> of <?= $search['count'] ?> results
	<?php endif; ?>
</div>

<div class="map-panel">
	<div class="map-overlay">Click to expand map</div>
	<div id="map" style="height:50px;"></div>
</div>
<?php partial("map", [
	'items' => R::exportAll($search['results']),
	'init' => true,
]); ?>

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
			<div class="discount-percentage-badge">
				<div class="discount-percentage-badge-wrapper">
					<div class="discount-percentage-badge-number">{{discount_percentage}}%</div>
					<div class="discount-percentage-badge-off">off</div>
				</div>
			</div>
		</div>
	</div>
	<div class="details">
		<div class="title" title="{{title}}">{{title}}</div>
		<div class="description">{{description}}</div>
	</div>
	<div class="store-info">{{shop.name}} - <span class="distance">?</span>mi</div>
</a>
</script>

<script>
$(function() {
	var resultItemTemplate = Handlebars.compile($('#result-item-template').html());
	var results = <?= json_encode(R::exportAll($search['results'])); ?>;

	for (var i = 0; i < results.length; i++) {
		var result = results[i];
		result.discount_percentage = calculateDiscount(result.discount_price, result.original_price);

		// Make this an object instead of a string so that we can use it in the calculateDistance callback
		var item = $(resultItemTemplate(result));
		$('.search-results').append(item);

		// Use separate function call otherwise item inside the closure gets bound to the last thing.
		doDistanceThing(result, item);
	}

	function doDistanceThing(result, item) {
		calculateDistance(result.shop.latitude, result.shop.longitude, function(distance) {
			item.find('.distance').text(niceRound(distance).toLocaleString());
		});
	}
});
</script>
<?php require __DIR__ . '/../includes/templates/footer.php'; ?>