<?php
require __DIR__ . '/../includes/start.php';

$q = getvar("q");
$category_id = (int)getvar("cat");

$category = null;
if ($category_id) {
	$category = R::load("category", $category_id);
}

$page = (int)getvar("page", 1);
$pageSize = 30;
$sort = getenum('sort', ['relevance', 'name', 'price'], 'relevance');
$dir = getenum('dir', ['asc', 'desc'], 'asc');
if ($sort == 'relevance')
	$dir = 'desc';

$products = [];
$capabilities = [];

$select = "SELECT id, title as name, image, original_price as price, MATCH(title) AGAINST(:query) as relevance FROM item WHERE MATCH(title) AGAINST(:query)";

if (!isRequestingJson()) {
	$products = R::getAll("
($select ORDER BY relevance DESC LIMIT :doublePageSize)
UNION ($select ORDER BY title LIMIT :pageSize)
UNION ($select ORDER BY title DESC LIMIT :pageSize)
UNION ($select ORDER BY original_price LIMIT :pageSize)
UNION ($select ORDER BY original_price DESC LIMIT :pageSize)",
			[':query' => $q, ':page' => ($page - 1) * $pageSize, ':pageSize' => $pageSize, ':doublePageSize' => 2 * $pageSize]);

	$totalProducts = (int)R::getCell('SELECT COUNT(*) FROM item WHERE MATCH(title) AGAINST (:query)', [':query' => $q]);
	$capabilities = ['sort' => ['name' => true, 'price' => true], 'pages' => ['next' => true]];
}

if ($page > 1 || isRequestingJson()) {
	$newProducts = R::getAll("
	($select ORDER BY $sort $dir LIMIT :start,:pageSize)",
		[':query' => $q, ':start' => ($page - 1) * $pageSize, ':pageSize' => $pageSize]);

	$ids = array_fill_keys(array_map(function($x) { return $x['id']; }, $products), true);
	$products = array_merge($products, array_filter($newProducts, function($x) use($ids) { return !array_key_exists($x['id'], $ids); }));
}


if (isRequestingJson()) {
	header('Content-Type: application/json');
	echo json_encode(['products' => $products, 'caps' => $capabilities]);
	die;
}

$nextPage = false; $nextUrl = '';
if (count($products) > $page * $pageSize) {
	$nextPage = $page + 1;
	$nextUrl = "?q=$q&sort=$sort&dir=$dir&page=$nextPage";
}
$prevPage = false; $prevUrl = '';
if ($page > 1) {
	$prevPage = $page - 1;
	$prevUrl = "?q=$q&sort=$sort&dir=$dir&page=$prevPage";
}
?>

<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<h1>Search Results</h1>
<?php partial("search-bar", ['q' => $q, 'url' => '']); ?>

<div class="result-count"><?= $totalProducts ?> Results</div>
<div class="sortby">
	<div>Sort by:</div>
	<div><a href=# class="sort-relevance <?= $sort == 'relevance' ? 'selected' : '' ?>">Relevance </a></div>
	<div><a href=# class="sort-name <?= $sort == 'name' ? $dir : '' ?>">Name </a></div>
	<div><a href=# class="sort-price <?= $sort == 'price' ? $dir : '' ?>">Price </a></div>
</div>
<a class="next-page <?= $nextPage ? '' : 'invisible' ?>" href="<?= $nextUrl ?>">Next Page</a>
<a class="prev-page <?= $prevPage ? '' : 'invisible' ?>" href="<?= $prevUrl ?>">Previous Page</a>


<div class="grid-wrapper"><ul class="product-grid"></ul></div>

<div class="clearfix">
<a class="next-page <?= $nextPage ? '' : 'invisible' ?>" href="<?= $nextUrl ?>">Next Page</a>
<a class="prev-page <?= $prevPage ? '' : 'invisible' ?>" href="<?= $prevUrl ?>">Previous Page</a>
</div>

<script type="text/x-handlebars" id="product-template">
	<li data-id="{{id}}" class="transitions">
		<div class="image" style="background-image: url('{{escapeQuotes image}}')"></div>
		<div class="name" title="{{name}}">{{name}}</div>
		<div class="price">${{price}}</div>
	</li>
</script>

<script>
$(function() {
	var products = <?= json_encode($products); ?>;
	var productsByID = products.toDictionary(function(x) { return x.id; });
	var totalProducts = <?= json_encode($totalProducts) ?>;
	var caps = <?= json_encode($capabilities); ?>;
	var currentSort = <?= json_encode($sort); ?>;
	var ascending = <?= $dir == 'desc' ? 'false' : 'true'; ?>;
	var pageSize = <?= json_encode($pageSize) ?>;
	var page = <?= json_encode($page) ?>;
	var query = <?= json_encode($q) ?>;
	var sorters = {
		relevance: function(a, b) { return (+a) - (+b); },
		name: function(a, b) { return a.localeCompare(b); },
		price: function(a, b) { return (+a) - (+b); }
	};

	var shownProducts = sortProducts();
	
	var productTemplate = Handlebars.compile($('#product-template').html());
	$('.product-grid').append(shownProducts.map(function(x) { return $(productTemplate(x)); }));

	$('.sort-relevance').click(function() {
		if (currentSort == 'relevance')
			return false;

		ascending = false;	// always sort by descending relevance
		currentSort = 'relevance';
		sort();
		$('.sortby>div>a').removeClass('asc desc selected');
		$(this).addClass('selected');
		return false;
	});

	$('.sort-name').click(function() {
		changeSort('name');
		return false;
	});

	$('.sort-price').click(function() {
		changeSort('price');
		return false;
	});

	function changeSort(field) {
		if (currentSort == field)
			ascending = !ascending;
		else
			ascending = true;

		currentSort = field;
		sort();

		$('.sortby>div>a').removeClass('asc desc selected');
		$('.sortby').find('.sort-' + field).addClass(ascending ? 'asc' : 'desc');
	}

	function sortProducts() {
		var sorter = sorters[currentSort];
		var shownProducts = products.slice();
		var m = ascending ? 1 : -1;
		shownProducts.sort(function(a, b) { return m * sorter(a[currentSort], b[currentSort]); });
		shownProducts.length = Math.min(shownProducts.length, pageSize);

		return shownProducts;
	}

	function sort() {
		page = 1;	// sorting puts us back on page 1
		updatePageLinks();
		var shownProducts = sortProducts();

		var grid = $('.product-grid');
		sort.elements = grid.children();

		var paddingElements = [];
		for (var i = grid.children().length; i < shownProducts.length; i++) {
			paddingElements.push($('<li>'));
		}

		grid.append(paddingElements);
		grid.css('max-height', grid.height());

		var elementsByID = {};
		
		if (!sort.positions) {		
			sort.positions = [];
			
			grid.children().each(function() {
				var r = this.getBoundingClientRect(); 
				var pos = { x: r.left, y: r.top }
				sort.positions.push(pos);
				if (this.childElementCount > 0) {
					$(this).data('pos', pos);
					elementsByID[$(this).data('id')] = $(this);
				}
			});
		}

		sort.newElements = [];
		var newIDs = {};
		
		for (var i = 0; i < shownProducts.length; i++) {
			var product = shownProducts[i];
			newIDs[product.id] = true;
			
			if (elementsByID[product.id]) {
				// The element has moved. Do a slide transition.
				var el = elementsByID[product.id];
				var x = sort.positions[i].x - el.data('pos').x;
				var y = sort.positions[i].y - el.data('pos').y;
				el.css('transform', 'translate(' + x + 'px, ' + y + 'px)');
				sort.newElements.push(el);
			} else {
				// The element is new. Do a fade in.
				var el = $(productTemplate(product));
				el.removeClass('transitions').css('opacity', 0);
				grid.append(el);
				var r = el[0].getBoundingClientRect();
				var x = sort.positions[i].x - r.left;
				var y = sort.positions[i].y - r.top;
				el.css('transform', 'translate(' + x + 'px, ' + y + 'px)');
					
				getComputedStyle(el[0]).opacity;	// force opacity to be applied
				el.addClass('transitions');
				el.css('opacity', 1);				// fade in
				sort.newElements.push(el);
			}
		}
		
		for (var i = 0; i < sort.elements.length; i++) {
			// These elements are gone from the page. Fade them out.
			if (!newIDs[$(sort.elements[i]).data('id')])
				sort.elements.eq(i).css('opacity', 0);
		}
		
		sort.elements.slice(pageSize).remove();

		var allEvents = [];
		sort.elements.each(function() {
			var $this = $(this);
			$this.off($.support.transition.end);
			var deferred = $.Deferred();
			allEvents.push(deferred);

			$this.one($.support.transition.end, function() {
				deferred.resolve();
			});
		});

		$.when.apply($, allEvents).then(function() {
			grid.empty();
			grid.append(sort.newElements).children().css({ opacity: '', transform: '' });
			grid.css('max-height', '');
			delete sort.positions;
		});

		prefetchNextPage();
	}

	$('.next-page').click(function() {
		if (!caps.pages.next) {
			var link = this;
			link.innerText = "Next Page..."
			$.getJSON(this.href, function(response) {
				mergeNewProducts(response);
				link.innerText = "Next Page";
				queueShowPage(page + 1);
			});
		} else {
			queueShowPage(page + 1);
			delete caps.pages.next;

			if (page * pageSize < totalProducts) {
				prefetchNextPage();
			}
		}

		return false;
	});

	$('.prev-page').click(function() {
		queueShowPage(page - 1);
		caps.pages.next = true;

		return false;
	});

	function prefetchNextPage(){
		$.getJSON($('.next-page')[0].href, function(response) {
			preloadImages(mergeNewProducts(response));
			caps.pages.next = true;
		});
	}

	function mergeNewProducts(response) {
		var newProducts = response.products.filter(function(x) { return !productsByID[x.id]; });
		products = products.concat(newProducts);
		$.extend(true, caps, response.caps);

		for (var i = 0; i < newProducts.length; i++)
			productsByID[newProducts[i].id] = newProducts[i];

		return newProducts;
	}

	function queueShowPage(pageNum) {
		$(window).queue('showPage', function() { showPage(pageNum); });
		if (!showPage.inProgress)
			$(window).dequeue('showPage');
	}

	function showPage(pageNum) {
		if (page == pageNum) {
			showPage.inProgress = false;
			$(window).dequeue('showPage');
			return;
		}

		showPage.inProgress = true;
		var sorter = sorters[currentSort];
		var shownProducts = products.slice();
		var m = ascending ? 1 : -1;
		shownProducts.sort(function (a, b) {
			return m * sorter(a[currentSort], b[currentSort]);
		});

		var dir = pageNum > page ? 1 : -1;
		page = pageNum;
		var start = (page - 1) * pageSize;
		shownProducts = shownProducts.slice(start, start + pageSize);

		var grid = $('.product-grid');
		grid.addClass('transitions');
		var r = grid[0].getBoundingClientRect();
		var w = $(window).width();
		var x = dir > 0 ? -r.right : w - r.left;
		grid.css('transform', 'translate(' + x + 'px, 0px');
		x = dir > 0 ? w - r.left : -r.right;
		var y = -r.height - parseInt(grid.css('margin-bottom'));
		var newGrid = $('<ul class="product-grid transitions">').css({
			transform: 'translate(' + x + 'px, ' + y + 'px)'
		});

		newGrid.append(shownProducts.map(function(x) { return $(productTemplate(x)); }));
		$('.grid-wrapper').append(newGrid).css('max-height', Math.max(grid.height(), newGrid.height()));
		newGrid[0].getBoundingClientRect();
		newGrid.css('transform', 'translate(0px, ' + y + 'px)');
		grid.one($.support.transition.end, function() {
			newGrid.removeClass('transitions').css('transform', '');
			grid.remove();
			$('.grid-wrapper').css('max-height', '');
			setTimeout(function() {
				showPage.inProgress = false;
				$(window).dequeue('showPage');
			}, 1);
		});

		updatePageLinks();

		if ($('.sortby')[0].getBoundingClientRect().top < 0)
			$('.sortby').velocity('scroll', { offset: -60 });
	}

	function updatePageLinks() {
		$('.next-page')[0].href = '?' + $.param({ q: query, sort: currentSort, dir: ascending ? 'asc' : 'desc', page: page + 1 });
		$('.prev-page')[0].href = '?' + $.param({ q: query, sort: currentSort, dir: ascending ? 'asc' : 'desc', page: page - 1 });
		$('.next-page').setClass('invisible', page * pageSize >= totalProducts);
		$('.prev-page').setClass('invisible', page <= 1);
	}

	$(window).load(function() {
		preloadImages(products);
	});

	function preloadImages(products) {
		for (var i = 0; i < products.length; i++) {
			var img = new Image();
			img.src = products[i].image;
		}
	}
});
</script>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>