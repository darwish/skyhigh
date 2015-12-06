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
		window.setTimeout(function(){location=location}, 1000);
		});
    },
};
</script>

<div class="bs-example">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#inventory">Inventory</a></li>
        <li><a data-toggle="tab" href="#analytics">Analytics</a></li>
    </ul>
    <div class="tab-content">
        <div id="inventory" class="tab-pane fade in active">
            <table class="table table-condensed">
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
            <form id="deadend" action="/upload-deadend.php" class="dropzone"></form>
        </div>
        <div id="analytics" class="tab-pane fade">
            <h1>Gizmo Sale Campaign <i>August 2014</i></h1>
            <!-- this will get populated with a d3.js plot -->
        </div>
    </div>
</div>

<script type="text/plain" id="result-item-template">
    <tr>
        <td>{{title}}</td>
        <td>{{formatMoney original_price}}</td>
        <td width="100px"><input type="range" min="0" max="100" value={{discount_percentage}} /></td>
        <td>{{formatMoney discount_price}}</td>
    </tr>
</script>

<script>
$(function() {
    var resultItemTemplate = Handlebars.compile($('#result-item-template').html());
    var results = <?= json_encode(R::exportAll(search('',1)['results'])); ?>;
    results.forEach(function(result){
        result.discount_percentage = -calculateDiscount(result.discount_price, result.original_price);
        var item = resultItemTemplate(result);
        $('#tbody').append(item);
    })
});
</script>

<script src="http://d3js.org/d3.v3.js"></script>
<script src="js/d3app.js"></script>
<link href="css/d3app.css" rel="stylesheet"  />


<?php require __DIR__ . '/../includes/templates/footer.php'; ?>
