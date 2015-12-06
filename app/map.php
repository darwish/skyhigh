<div class="container fill">
	<div class="panel panel-default">
	<div class="panel-heading">Map</div>
		<div id="map-panel" class="panel-body">
			<div id="map"></div>
		</div>
	</div>
 </div>

 <script>
 	var mapLat = 37.7833;
	var mapLon = -122.4167;

	L.mapbox.accessToken = 'pk.eyJ1IjoiY3JvY29kb3lsZSIsImEiOiJjaWhpZzRlY2MwbXFqdGNsenRqZmxqMHBrIn0.7yc8ndkeNHCD1TxhFzwe6w';
	var map = L.mapbox.map('map', 'mapbox.dark').setView([mapLat, mapLon], 16);

	var dealPoints = new Array();
	for(i=0; i<10; i++){
		for(j=0; j<10; j++){
			dealPoints[i*10+j] = {};
			dealPoints[i*10+j].lat = mapLat + (i+1)*0.001,
			dealPoints[i*10+j].lon = mapLon + (j+1)*0.001;
			dealPoints[i*10+j].storeName = 'joes';
			dealPoints[i*10+j].itemName = 'shoes';
			dealPoints[i*10+j].percentOff = 50;
			dealPoints[i*10+j].imgUrl = 'http://placebear.com/32/32';
			dealPoints[i*10+j].id = 4523;
		}
	}

	for (i=0; i< dealPoints.length; i++){
		addItem(dealPoints[i]);
	}

	//var userLocation = L.marker([mapLat, mapLon]).addTo(map);
	var userLocation = L.AwesomeMarkers.icon({
		icon: 'home',
		markerColor: 'red'
	});

  L.marker([mapLat,mapLon], {icon: userLocation}).addTo(map);


	function addItem(item){
		var itemIcon = L.AwesomeMarkers.icon({
			icon: 'shopping-cart',
			markerColor: 'blue'
		});

		var options = {};
		options.title = item.percentOff + '% off ' + item.itemName;
		options.icon = itemIcon;

		mark = L.marker([item.lat, item.lon], options).addTo(map);
		mark.bindPopup('<a href="item.php?q=' + item.id + '"><img src="' + item.imgUrl + '" /> &nbsp; ' + item.itemName + '</a>').openPopup();
	}

	</script>

	<div id='log'></div>
