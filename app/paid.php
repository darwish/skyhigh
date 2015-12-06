<?php
require __DIR__ . '/../includes/start.php';

// .../paid.php
// ?id=428
// &amount=4999
// &paymentId=aodbqXyA
// &paymentDate=1449371641859
// &paymentStatus=APPROVED
// &authCode=0VJVQH
// &currency=USD
// &signature=F7C868142562D337A446381F8C1A7258
// &reference=722

$amount = getvar("amount");
$paymentId = getvar("paymentId");
$paymentDate = getvar("paymentDate");
$paymentStatus = getvar("paymentStatus");
$authCode = getvar("authCode");
$currency = getvar("currency");
$signature = getvar("signature");
$item_id = $reference = getvar("reference");

$item = findItem($item_id);

if (!$item) {
	throw new Exception("Could not find item for ID $item_id. That's bad.");
}

if ($paymentStatus === "APPROVED") {
	$purchase = R::dispense("purchase");
	$purchase->item = $item;
	$purchase->user = me();

	$purchase->amount = $amount;
	$purchase->paymentId = $paymentId;
	$purchase->paymentDate = $paymentDate;
	$purchase->paymentStatus = $paymentStatus;
	$purchase->authCode = $authCode;
	$purchase->currency = $currency;

	R::store($purchase);

	redirect("item.php?id={$item->id}");
} else {
	redirect("item.php?id={$item->id}&error=true");
}