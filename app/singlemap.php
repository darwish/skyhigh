<?php
require __DIR__ . '/../includes/start.php';

$item_id = getvar("id");
$item = findItem($item_id);
$item->shop; // Load that data for export

$purchase = null;
if (me()) {
	$purchase = findPurchase($item->id, me()->id);
}

$redirectUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . preg_replace('/item\.php/', 'paid.php', $_SERVER['REQUEST_URI']);

$pageTitle = "Purchase Item";
?>

<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<?php partial("map", ['items' => [$item->export()]]); ?>

<div id="map"></div>

<script>$(function() { initMap(); });</script>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>

<style>
/* show only map */
#map {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
nav {
    display: none;
}
</style>
