<script>
function initMap() {
	var mapLat = 37.7833;
	var mapLon = -122.4167;
	var markers = [];
	var markersByPosition = {};
	var items = <?= json_encode($items); ?>;

	L.mapbox.accessToken = 'pk.eyJ1IjoiY3JvY29kb3lsZSIsImEiOiJjaWhpZzRlY2MwbXFqdGNsenRqZmxqMHBrIn0.7yc8ndkeNHCD1TxhFzwe6w';
	var map = window.map = L.mapbox.map('map', 'mapbox.dark', { scrollWheelZoom: false });

	var userLocation = L.AwesomeMarkers.icon({
		icon: 'home',
		markerColor: 'red'
	});

	var userMarker = L.marker([ mapLat, mapLon ], { icon: userLocation }).addTo(map);
	markers.push(userMarker);

	for (var i = 0; i < items.length; i++) {
		addItem(items[i]);
	}

	_fitMapBounds();

	function addItem(item) {
		var discountPercentage = calculateDiscount(item.discount_price, item.original_price);

		var link =
			'<a href="item.php?id=' + item.id + '" class="leaflet-popup-item">' +
				'<img class="leaflet-popup-item-image" src="' + item.image + '" width="50">' +
				'<span class="leaflet-popup-item-name">' +
					item.title + '<br>' +
					'<span class="leaflet-popup-item-price">' +
						'<b>' + formatMoney(item.discount_price) + '</b>' +
						' (' + discountPercentage + '% off)'
					'</span>' +
				'</span>' +
			'</a>'
		;

		// Check for existing marker and add to it if it exists
		var posString = item.shop.latitude + "," + item.shop.longitude;
		if (posString in markersByPosition) {
			var marker = markersByPosition[posString];
			marker.getPopup().setContent(marker.getPopup().getContent() + link);
			return;
		}

		var itemIcon = L.AwesomeMarkers.icon({
			icon: 'shopping-cart',
			markerColor: 'blue'
		});

		var options = {
			title: discountPercentage + '% off ' + item.title,
			icon: itemIcon
		};

		var mark = L.marker([item.shop.latitude, item.shop.longitude], options).addTo(map);
		markers.push(mark);

		mark.bindPopup(link);

		markersByPosition[posString] = mark;
	}

	window.fitMapBounds = function() {
		_fitMapBounds();
	};

	function _fitMapBounds() {
		map.fitBounds(new L.featureGroup(markers), { padding: [20, 20] });
	}
}

<?php if (!empty($init)): ?>
$(initMap);
<?php endif; ?>
</script>