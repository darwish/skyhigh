<?php require __DIR__ . '/../includes/start.php'; ?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<script src="js/vendor/dropzone.js"></script>
<link href="css/dropzone.css" rel="stylesheet"  />
<link href="css/dashboard.css" rel="stylesheet"  />
<script>$("title").html("Dashboard")</script>

<script>
Dropzone.options.deadend = {
    dictDefaultMessage: "Add files",
    acceptedFiles: ".csv",
    init: function() {
        this.on("complete", function(file) {
		window.setTimeout(function(){location=location+'?upload'}, 1000);
		});
    },
};
</script>

<div class="dashboard-container">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#inventory">Inventory</a></li>
        <li><a data-toggle="tab" href="#coupon-settings">Coupon Settings</a></li>
        <li><a data-toggle="tab" href="#analytics">Analytics</a></li>
        <li><a data-toggle="tab" href="#store-details">Store Details</a></li>
    </ul>
    <div class="tab-content row">
        <div id="inventory" class="tab-pane fade in active">
            <table class="table table-condensed inventory-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Original Price</th>
                        <th>Discount %</th>
                        <th>Discount Price</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                <!-- this will get populated by a Handlebars.js template -->
                </tbody>
            </table>
            <!-- this is a Dropzone.js magic widget -->
            <h1>Import Inventory</h1>
            <form id="deadend" action="/upload-deadend.php" class="dropzone"></form>
        </div>
        <div id="coupon-settings" class="tab-pane fade">
            <form action="#" class="row">
                <div class="col-md-4">
                    <h1>Coupon Defaults</h1>
                    <div class="form-group"><label for="default-duration">How long before a coupon expires?</label><input class="form-control" type="text" value="1 day" id="default-duration"></div>
                    <div class="form-group"><label for="coupons-per-visit">How many coupons can each customer use?</label><input class="form-control" type="text" value="1" id="coupons-per-visit"></div>
                    <div class="form-group"><label for="coupon-frequency">How often can customers use coupons?</label><div>Every <input class="form-control" type="number" id="coupon-frequency" value="2"> days</div></div>
                    <div class="checkbox"><label><input type="checkbox"> Cycle offers</label></div>
                </div>
            </form>
        </div>
        <div id="analytics" class="tab-pane fade">
            <h1>Gizmo Sale Campaign <i>August 2014</i></h1>
            <!-- this will get populated with a d3.js plot -->
        </div>
         <div id="store-details" class="tab-pane fade ">
            <form class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="address1">Street Address</label>
                    <input id="address1" class="form-control" type="text" value="123 Main Street">
                </div>
                <div class="form-group">
                    <label for="zip">Zip Code</label>
                    <input id="zip" class="form-control" type="text" value="12345">
                </div>

                <button id="fetch-places" class="btn btn-primary" disabled>Save</button>
            </div>
            <div class="col-md-8">
                <input name="location-lat" type="hidden">
                <input name="location-lon" type="hidden">
                <div id="map" style="height: 500px;"></div>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/plain" id="result-item-template">
    <tr>
        <td>{{title}}</td>
        <td class="original-price">{{formatMoney original_price}}</td>
        <td width="100px"><input class="form-control" type="number" value={{discount_percentage}} min="0" max="100" /></td>
        <td class="discount-price">{{formatMoney discount_price}}</td>
    </tr>
</script>

<script>
$(function() {
    $('.inventory-table').on('input', 'input', function() {
        var row = $(this).closest('tr');
        var price = +row.find('.original-price').text().substr(1);
        row.find('.discount-price').text(formatMoney(price * (1 - $(this).val() / 100)));
    });

    var resultItemTemplate = Handlebars.compile($('#result-item-template').html());
    var results = <?= json_encode(R::exportAll(search('',1)['results'])); ?>;
    if (location.toString().contains('upload')) {
        results.forEach(function(result){
            result.discount_percentage = calculateDiscount(result.discount_price, result.original_price);
            var item = resultItemTemplate(result);
            $('#tbody').append(item);
        });
    }

    var addressField = $('#address1');
    var zipField = $('#zip');
    var placesRequest;

    addressField.on('input', fetchPlaces);
    zipField.on('input', fetchPlaces);

    function fetchPlaces() {
        if (placesRequest) {
            placesRequest.abort();
        }

        var zip = zipField.val();
        var street = addressField.val();

        if (zip.length !== 5) {
            return;
        }
        if (street.length == 0) {
            return;
        }

        // placesRequest = $.get('http://dmartin.org:8006/merchantpoi/v1/merchantpoisvc.svc/merchantpoi?postalCode='+zip+'&streetAddr='+street+'&format=JSON');
        // placesRequest.success(function(response, status, xhr) {

            // the places api has data that is too sparse for our demo at this time
            // we are assuming that the following data is returned for demo purposes
            var data = {
                MerchantPOIList: {
                    Count: "21",
                    MerchantPOIArray: {
                        MerchantPOI: [
                            {
                                AggregateMerchantId: "5611",
                                AggregateMerchantName: "NON-AGGREGATED MEN'S AND BOY'S CLOTHING AND ACCESSORIES STOR 5611",
                                CleansedCityName: "SAN FRANCISCO",
                                CleansedCountryCode: "USA",
                                CleansedMerchantName: "AG ADRIANO GOLDSCHMIED",
                                CleansedMerchantPostalCode: "94108-5808",
                                CleansedMerchantStreetAddress: "20 OFARRELL ST",
                                CleansedMerchantTelephoneNumber: "(415) 398-0546",
                                CleansedStateProvidenceCode: "CA",
                                CuisineCode: null,
                                DMACode: "807",
                                GeocodeQualityIndicator: "S8",
                                HiddenGem: "N",
                                InBusinessFlag: "Y",
                                Industry: "AAM",
                                KeyAggregateMerchantId: "5611",
                                Latitude: "37.786865999999996",
                                LocalFavorite: "N",
                                Longitude: "-122.405552",
                                MCCCode: "5611",
                                MSACode: "7360",
                                MerchantCityName: "SAN FRANCISCO",
                                MerchantCountryCode: "USA",
                                MerchantName: "AG SAN FRANCISCO",
                                MerchantPostalCode: "94108",
                                MerchantStateProvidenceCode: "CA",
                                MerchantStreetAddress: "20 OFARRELL ST",
                                NAICSCode: "424320",
                                NewBusinessFlag: "N",
                                ParentAggregateMerchantId: null,
                                ParentAggregateMerchantName: "NON-AGGREGATED",
                                PrimaryChannelOfDistribution: "B",
                                SuperIndustry: "AAP"
                            },
                        ]
                    }
                }
            };

            if (data.MerchantPOIList.Count) {
                var lat = data.MerchantPOIList.MerchantPOIArray.MerchantPOI[0].Latitude;
                var lon = data.MerchantPOIList.MerchantPOIArray.MerchantPOI[0].Longitude;

                L.mapbox.accessToken = 'pk.eyJ1IjoiY3JvY29kb3lsZSIsImEiOiJjaWhpZzRlY2MwbXFqdGNsenRqZmxqMHBrIn0.7yc8ndkeNHCD1TxhFzwe6w';
                var map = L.mapbox.map('map', 'mapbox.dark').setView([lat, lon], 16);

                var mark = L.marker([lat, lon]).addTo(map);
                //mark.bindPopup('<a href="item.php?q="><img src="' + item.imgUrl + '" /> &nbsp; ' + item.item + '</a>').openPopup();
            } else {
                alert('something went wrong');
            }

        // });
    }
});
</script>

<script src="http://d3js.org/d3.v3.js"></script>
<script src="js/d3app.js"></script>
<link href="css/d3app.css" rel="stylesheet"  />


<?php require __DIR__ . '/../includes/templates/footer.php'; ?>
