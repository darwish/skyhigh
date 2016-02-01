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
$sort = getenum('sort', ['name', 'price'], 'name');
$dir = getenum('dir', ['asc', 'desc'], 'asc');

if ($page == 1 && $sort == 'name' && $dir == 'asc') {
	$products = R::getAll("
	(SELECT id, title as name, image, original_price as price FROM item  WHERE MATCH(title) AGAINST(:query) ORDER BY title LIMIT :doublePageSize)
	UNION (SELECT id, title as name, image, original_price as price FROM item  WHERE MATCH(title) AGAINST(:query) ORDER BY title DESC LIMIT :pageSize)
	UNION (SELECT id, title as name, image, original_price as price FROM item  WHERE MATCH(title) AGAINST(:query) ORDER BY original_price LIMIT :pageSize)
	UNION (SELECT id, title as name, image, original_price as price FROM item  WHERE MATCH(title) AGAINST(:query) ORDER BY original_price DESC LIMIT :pageSize)",
			[':query' => $q, ':page' => ($page - 1) * $pageSize, ':pageSize' => $pageSize, ':doublePageSize' => 2 * $pageSize]);

	$totalProducts = (int)R::getCell('SELECT COUNT(*) FROM item WHERE MATCH(title) AGAINST (:query)', [':query' => $q]);
	$capabilities = ['sort' => ['name' => true, 'price' => true], 'pages' => ['next' => true ]];
} else {
	$products = R::getAll("
	(SELECT id, title as name, image, original_price as price FROM item  WHERE MATCH(title) AGAINST(:query) ORDER BY :sort :dir LIMIT :start,:pageSize)",
		[':query' => $q, 'start' => ($page - 1) * $pageSize, ':pageSize' => $pageSize, ':sort' => $sort, ':dir' => $dir]);

	$capabilities = [];
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
<?php partial("search-bar", ['q' => $q]); ?>

<div class="sortby">
	<div>Sort by:</div>
	<div><a href=# class="sort-name asc">Name </a></div>
	<div><a href=# class="sort-price">Price </a></div>
</div>
<a class="next-page <?= $nextPage ? '' : 'hidden' ?>" href="<?= $nextUrl ?>">Next Page</a>
<a class="prev-page <?= $prevPage ? '' : 'hidden' ?>" href="<?= $prevUrl ?>">Previous Page</a>


<div class="grid-wrapper"><ul class="product-grid"></ul></div>

<div class="clearfix">
<a class="next-page <?= $nextPage ? '' : 'hidden' ?>" href="<?= $nextUrl ?>">Next Page</a>
<a class="prev-page <?= $prevPage ? '' : 'hidden' ?>" href="<?= $prevUrl ?>">Previous Page</a>
</div>

<script type="text/x-handlebars" id="product-template">
	<li data-id="{{id}}" class="transitions">
		<div class="image" style="background-image: url({{image}})"></div>
		<div class="name">{{name}}</div>
		<div class="price">${{price}}</div>
	</li>
</script>

<script>
$(function() {
	var products = <?= json_encode($products); ?>;
	var productsByID = products.toDictionary(function(x) { return x.id; });
	var totalProducts = <?= json_encode($totalProducts) ?>;
	var caps = <?= json_encode($capabilities); ?>;
	var currentSort = "name";
	var ascending = true;
	var pageSize = <?= json_encode($pageSize) ?>;
	var page = <?= json_encode($page) ?>;
	var query = <?= json_encode($q) ?>;
	var sorters = { 
		name: function(a, b) { return a.localeCompare(b); },
		price: function(a, b) { return (+a) - (+b); }
	};

	var shownProducts = products.slice(); // copy
	shownProducts.sort(function(a, b) { return a.name.localeCompare(b.name); });
	shownProducts.length = pageSize;
	
	var productTemplate = Handlebars.compile($('#product-template').html());
	$('.product-grid').append(shownProducts.map(function(x) { return $(productTemplate(x)); }));

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

		$('.sortby>div>a').removeClass('asc desc');
		$('.sortby').find('.sort-' + field).addClass(ascending ? 'asc' : 'desc');
	}

	function sort() {
		page = 1;	// sorting puts us back on page 1
		updatePageLinks();

		var sorter = sorters[currentSort];
		var shownProducts = products.slice();
		var m = ascending ? 1 : -1;
		shownProducts.sort(function(a, b) { return m * sorter(a[currentSort], b[currentSort]); });
		shownProducts.length = Math.min(shownProducts.length, pageSize);

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
				var el = elementsByID[product.id];
				var x = sort.positions[i].x - el.data('pos').x;
				var y = sort.positions[i].y - el.data('pos').y;
				el.css('transform', 'translate(' + x + 'px, ' + y + 'px)');
				sort.newElements.push(el);
			} else {
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
	}

	$('.next-page').click(function() {
		if (!caps.pages.next) {
			var link = this;
			link.innerText = "Next Page..."
			$.getJSON(this.href, function(response) {
				products = products.concat(response.products.filter(function(x) { return !productsByID[x.id]; }));
				$.extend(true, caps, response.caps);
				link.innerText = "Next Page";
				showPage(page + 1);
			});
		} else {
			showPage(page + 1);
			delete caps.pages.next;
		}

		return false;
	});

	function showPage(pageNum) {
		var sorter = sorters[currentSort];
		var shownProducts = products.slice();
		var m = ascending ? 1 : -1;
		shownProducts.sort(function (a, b) {
			return m * sorter(a[currentSort], b[currentSort]);
		});

		page = pageNum;
		var start = (page - 1) * pageSize;
		shownProducts = shownProducts.slice(start, start + pageSize);

		var grid = $('.product-grid');
		grid.addClass('transitions');
		var r = grid[0].getBoundingClientRect();
		grid.css('transform', 'translate(' + (-r.right) + 'px, 0px');
		var x = $(window).width() - r.left;
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
		});

		updatePageLinks();
	}

	function updatePageLinks() {
		$('.next-page')[0].href = '?' + $.param({ q: query, sort: currentSort, dir: ascending ? 'asc' : 'desc', page: page + 1 });
		$('.prev-page')[0].href = '?' + $.param({ q: query, sort: currentSort, dir: ascending ? 'asc' : 'desc', page: page - 1 });
		$('.next-page').setClass('hidden', page * pageSize >= totalProducts);
		$('.prev-page').setClass('hidden', page <= 1);
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