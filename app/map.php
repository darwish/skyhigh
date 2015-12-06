<div class="container fill">
	<div class="panel panel-default" style="height:500px">
	<div class="panel-heading">Map</div>
		<div class="panel-body">
			<div id="map" style="height:500px"></div>
		</div>
	</div>
 </div>


	<script>
	
	var mapLat = 37.7833;
	var mapLon = -122.4167;
	
	L.mapbox.accessToken = 'pk.eyJ1IjoiY3JvY29kb3lsZSIsImEiOiJjaWhpZzRlY2MwbXFqdGNsenRqZmxqMHBrIn0.7yc8ndkeNHCD1TxhFzwe6w';
	var map = L.mapbox.map('map', 'mapbox.dark')
		.setView([mapLat, mapLon], 20);
		
		
	var dealPoints = new Array();
	for(i=0; i<10; i++){
		for(j=0; j<10; j++){
			dealPoints[i*10+j] = {};
			dealPoints[i*10+j].lat = mapLat + (i+1)*0.0001,
			dealPoints[i*10+j].lon = mapLon + (j+1)*0.0001;
			dealPoints[i*10+j].bizName = 'joes';
			dealPoints[i*10+j].item = 'shoes';
			dealPoints[i*10+j].percentOff = 50;
		}
	}
	
	for (i=0; i< dealPoints.length; i++){
		//$( "div.log" ).html(deal.lat);
		
		mark = L.marker([dealPoints[i].lat, dealPoints[i].lon]).addTo(map);
		mark.bindPopup("THIS IS AN ITEM").openPopup();
	}
	
	
	</script>

	<div id='log'></div>