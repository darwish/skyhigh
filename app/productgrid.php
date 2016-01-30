<?php
require __DIR__ . '/../includes/start.php';

$products = R::getAll('SELECT image, title, original_price FROM item WHERE id <= 90');
?>

<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<h1>Search Results</h1>
<div class="sortby">
	<div>Sort by:</div>
	<div><a href=# class="sort-name asc">Name </a></div>
	<div><a href=# class="sort-price">Price </a></div>
</div>
<ul class="product-grid"></ul>

<script type="text/x-handlebars" id="product-template">
	<li>
		<img src="{{image}}">
		<div class="name">{{title}}</div>
		<div class="price">${{original_price}}</div>
	</li>
</script>

<script>
$(function() {
	var products = <?= json_encode($products); ?>;
	var currentSort = "name";
	var ascending = true;

	var productTemplate = Handlebars.compile($('#product-template').html());
	$('.product-grid').append(products.map(function(x) { return $(productTemplate(x)); }));

	$('.sort-name').click(function() {
		sort('name');
		return false;
	});

	$('.sort-price').click(function() {
		sort('price');
		return false;
	});

	var selectors = { 
		name: function(a, b) { return a.localeCompare(b); },
		price: function(a, b) { return (+a.substr(1)) - (+b.substr(1)); }
	};

	function sort(field) {
		if (currentSort == field) 
			ascending = !ascending;
		else
			ascending = true;
			
		currentSort = field;
		sort.elements = $('.product-grid').children();
		
		if (!sort.positions) {		
			sort.positions = [];
			
			sort.elements.each(function() { 
				var r = this.getBoundingClientRect(); 
				var pos = { x: r.left, y: r.top }
				$(this).data('pos', pos); 
				sort.positions.push(pos);
			});
		}
		
		var s = selectors[field];

		sort.elements.sort(function(a, b) {
			var m = ascending ? 1 : -1;
			return m * s($(a).find('.' + currentSort).text(), $(b).find('.' + currentSort).text());
		});
		
		for (var i = 0; i < sort.elements.length; i++) {
			var el = sort.elements.eq(i);
			var x = sort.positions[i].x - el.data('pos').x;
			var y = sort.positions[i].y - el.data('pos').y;
			$(sort.elements[i]).css('transform', 'translate(' + x + 'px, ' + y + 'px)');
		}
		
		if (sort.elements.length > 0) {
			sort.elements.off($.support.transition.end);
			sort.elements.eq(0).one($.support.transition.end, function() {
				sort.elements.remove().css('transform', '');			
				$('.product-grid').append(sort.elements);
				delete sort.positions;
			});
		}
		
		$('.sortby>div>a').removeClass('asc desc');
		$('.sortby').find('.sort-' + field).addClass(ascending ? 'asc' : 'desc');
	}
});
</script>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>