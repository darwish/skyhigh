<script>
function initMap() {
	var mapLat = 37.7833;
	var mapLon = -122.4167;
	var markers = [];
	var markersByPosition = {};
	var items = <?= json_encode($items); ?>;

	L.mapbox.accessToken = 'pk.eyJ1IjoiY3JvY29kb3lsZSIsImEiOiJjaWhpZzRlY2MwbXFqdGNsenRqZmxqMHBrIn0.7yc8ndkeNHCD1TxhFzwe6w';

	var dealPoints = [];
	for (var i = 0; i < items.length; i++) {
		var item = items[i];
		var shop = item.shop;

		dealPoints.push({
			lat        : shop.latitude,
			lon        : shop.longitude,
			storeName  : shop.name,
			itemName   : item.title,
			percentOff : calculateDiscount(item.discount_price, item.original_price),
			imgUrl     : item.image,
			id         : item.id
		});
	}

	var map = L.mapbox.map('map', 'mapbox.dark');

	for (var i = 0; i < dealPoints.length; i++) {
		addItem(dealPoints[i]);
	}

	var userLocation = L.AwesomeMarkers.icon({
		icon: 'home',
		markerColor: 'red'
	});

	markers.push(L.marker([mapLat,mapLon], {icon: userLocation}).addTo(map));

	map.fitBounds(new L.featureGroup(markers), { padding: [10, 10] });

	function addItem(item){
		var link =
			'<a href="item.php?id=' + item.id + '" class="leaflet-popup-item">\
				<img class="leaflet-popup-item-image" src="' + item.imgUrl + '" width="50">' +
				'<span class="leaflet-popup-item-name">' + item.itemName + '</span>\
			</a>\
		';

		// Check for existing marker and add to it if it exists
		var posString = item.lat + "," + item.lon;
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
			title: item.percentOff + '% off ' + item.itemName,
			icon: itemIcon
		};

		var mark = L.marker([item.lat, item.lon], options).addTo(map);
		markers.push(mark);

		mark.bindPopup(link);

		markersByPosition[posString] = mark;
	}
}

<?php if (!empty($init)): ?>
$(initMap);
<?php endif; ?>
</script>