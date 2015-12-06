
<div class='row'>
	<div id='map'></div>
</div>


	<script>
	
	var mapLat = 37.7833;
	var mapLon = -122.4167;
	
	L.mapbox.accessToken = 'pk.eyJ1IjoiY3JvY29kb3lsZSIsImEiOiJjaWhpZzRlY2MwbXFqdGNsenRqZmxqMHBrIn0.7yc8ndkeNHCD1TxhFzwe6w';
	var map = L.mapbox.map('map', 'mapbox.dark')
		.setView([mapLat, mapLon], 9);
		
		
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
	
	for (deal in dealPoints){
		$( "div.log" ).html(deal.lat);

		//L.marker([deal.lat, deal.lon]).addTo(map);
	}
	
	
	</script>

	<div id='log'></div>