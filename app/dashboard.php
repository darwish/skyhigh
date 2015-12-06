<?php require __DIR__ . '/../includes/start.php'; ?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<script src="http://d3js.org/d3.v3.js"></script>
<script src="js/d3app.js"></script>
<link href="css/d3app.css" rel="stylesheet"  />
<script>$("title").html("Dashboard")</script>

<div class="bs-example">
    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#sectionA">Inventory</a></li>
        <li><a data-toggle="tab" href="#sectionB">Analytics</a></li>
    </ul>
    <div class="tab-content">
        <div id="sectionA" class="tab-pane fade in active">
            adsfasdf <?= json_encode(search("any_string", 1)["results"]); ?>
        </div>
        <div id="sectionB" class="tab-pane fade"></div>
    </div>
</div>


<?php require __DIR__ . '/../includes/templates/footer.php'; ?>
