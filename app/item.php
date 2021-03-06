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

<h1>Purchase Item</h1>

<?php partial("breadcrumbs", ['category' => $item->category]); ?>

<?php if (!$item): ?>
	<div class="alert alert-danger">
		That item doesn't appear to exist. Try searching for a different one.
	</div>

	<?php partial("search-bar", ['q' => ""]); ?>
<?php else: ?>

	<?php if ($purchase): ?>
	<div class="alert alert-success">
		  <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> Your payment has been processed! Show the QR code below to the cashier as proof of purchase.
	</div>
	<?php endif; ?>

	<div class="item-container"></div>
	<?php partial("map", ['items' => [$item->export()]]); ?>

	<script type="text/plain" id="item-template">
	<div class="purchase-item clearfix">
		<div class="thumbnail item-thumbnail"><img src="{{image}}"></div>

		<div class="pay-buttons">
			<div class="thumbnail">
				<img src="https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl={{id}}:<?=me() ? me()->id : 0?>">
				<div class="caption">
					<?php if ($purchase === null): ?>
						<small>Show this QR code to the cashier to redeem your personalized coupon for {{discount_percentage}}% off.</small>
					<?php else: ?>
						<small>Show this QR code to the cashier as proof of purchase.</small>
					<?php endif; ?>
				</div>
			</div>
			<?php if (!me()): ?>
			<button class="btn btn-lg btn-success" data-toggle="modal" data-target="#signin-modal">Pay Now</button>
			<?php elseif (!$purchase): ?>
			<button id="pay-now"
			        data-sc-key="sbpb_ZjVlZGMzMTctYTY4MS00MTA5LWJiM2MtYmMwZGE0ZTMzZGZi"
			        data-name="{{title}}"
			        data-description="{{substr description 0 255}}"
			        data-reference="{{id}}"
			        data-amount="{{discount_price_cents}}"
			        data-redirect-url="<?= $redirectUrl ?>"
			        data-masterpass="true"
			        data-color="#12B830">
				<img src="css/images/Master-Card-icon-sm.png"></img>&nbsp;Pay Now
			</button>
			<?php else: ?>
			<button class="btn btn-lg btn-success" disabled><span class="glyphicon glyphicon-ok"></span> Paid</button>
			<?php endif; ?>
		</div>

		<div class="details">
			<div class="title" title="{{title}}">{{title}}</div>
			<div class="description">{{description}}</div>
		</div>

		<div class="price well">
			<div class="prices">
				<div class="discount-price">{{formatMoney discount_price}}</div>
				<div class="original-price-text">Original price: <span class="original-price">{{formatMoney original_price}}</span></div>
			</div>
			<div class="discount-percentage">
				<div class="discount-percentage-badge">
					<div class="discount-percentage-badge-wrapper">
						<div class="discount-percentage-badge-number">{{discount_percentage}}%</div>
						<div class="discount-percentage-badge-off">off</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default map-panel-container">
			<div class="panel-heading">Map</div>
			<div id="map-panel" class="panel-body">
				<div id="map"></div>
			</div>
		</div>
	</div>
	</script>

	<script>
	$(function() {
		var itemTemplate = Handlebars.compile($('#item-template').html());
		var data = <?= json_encode($item->export()); ?>;

		data.discount_percentage = calculateDiscount(data.discount_price, data.original_price);
		data.discount_price_cents = Math.round(data.discount_price * 100);

		// Make this an object instead of a string so that we can use it in the calculateDistance callback
		var item = $(itemTemplate(data));
		$('.item-container').append(item);

		initMap();

		calculateDistance(data.shop.latitude, data.shop.longitude, function(distance) {
			item.find('.distance').text(niceRound(distance).toLocaleString());
		});

		<?php if (!empty($_SESSION['pay'])): ?>
			<?php unset($_SESSION['pay']); ?>

			$('#loading-modal').modal('show');
			setTimeout(function() {
				$('#pay-now').click();
			}, 1000);
		<?php endif; ?>
	});
	</script>
<?php endif; ?>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>