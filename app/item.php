<?php
require __DIR__ . '/../includes/start.php';

$item_id = getvar("id");
$item = findItem($item_id);

$redirectUrl = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['HTTP_HOST'] . preg_replace('/item\.php/', 'paid.php', $_SERVER['REQUEST_URI']);

$pageTitle = "Purchase Item";
?>
<?php require __DIR__ . '/../includes/templates/header.php'; ?>

<h1>Purchase Item</h1>

<?php if (!$item): ?>
	<div class="alert alert-danger">
		That item doesn't appear to exist. Try searching for a different one.
	</div>

	<?php partial("search-bar", ['q' => ""]); ?>
<?php else: ?>

	<div class="item-container"></div>

	<script type="text/plain" id="item-template">
	<div class="purchase-item clearfix">
		<div class="thumbnail item-thumbnail"><img src="{{thumbnail}}"></div>

		<div class="pay-buttons">
			<div class="thumbnail">
				<a href="#qr-modal" data-toggle="modal" class="qr-modal-trigger">
					<img src="https://chart.googleapis.com/chart?cht=qr&chs=250x250&chl=asdfasdf">
				</a>
				<div class="caption">
					<small>Show this QR code to the cashier to redeem your personalized coupon for {{positive_discount_percentage}}% off.</small>
				</div>
			</div>
			<button id="pay-now"
			        data-sc-key="sbpb_ZjVlZGMzMTctYTY4MS00MTA5LWJiM2MtYmMwZGE0ZTMzZGZi"
			        data-name="{{title}}"
			        data-description="{{description}}"
			        data-reference="{{id}}"
			        data-amount="{{discount_price_cents}}"
			        data-redirect-url="<?= $redirectUrl ?>"
			        data-masterpass="true"
			        data-color="#12B830">
				Pay Now
			</button>
		</div>

		<div class="details">
			<div class="title">{{title}}</div>
			<div class="description">{{description}}</div>
		</div>

		<div class="price">
			<div class="prices">
				<div class="discount-price">{{formatMoney discount_price}}</div>
				<div class="original-price-text">Original price: <span class="original-price">{{formatMoney original_price}}</span></div>
			</div>
			<div class="discount-percentage">
				<div class="discount-percentage-badge">{{discount_percentage}}%</div>
			</div>
		</div>

		<div class="store-info">{{store}} - {{distance}}</div>
	</div>
	</script>

	<script>
	$(function() {
		var itemTemplate = Handlebars.compile($('#item-template').html());
		var item = <?= json_encode($item->export()); ?>;

		item.discount_percentage = calculateDiscount(item.discount_price, item.original_price);
		item.positive_discount_percentage = -calculateDiscount(item.discount_price, item.original_price);
		item.discount_price_cents = Math.round(item.discount_price * 100);

		var item = itemTemplate(item);
		$('.item-container').append(item);

		$('.qr-modal-trigger').click(function() {
			var src = $(this).find('img').prop('src');
			src = src.replace(/&chs=.*?&/, '&chs=500x500&');
			$('#qr-modal .qr-code').prop('src', src);
		});
	});
	</script>
<?php endif; ?>

<?php require __DIR__ . '/../includes/templates/footer.php'; ?>