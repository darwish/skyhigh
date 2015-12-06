<?php

function generateDB() {
	$storeData = json_decode(file_get_contents(__DIR__ . '/../data/stores.json'), true);

	$categories = R::findAll("category");

	foreach ($storeData as $store) {
		$store['_type'] = 'shop';
		pr($store);
		$storeBean = R::dispense($store);
		R::store($storeBean);
	}

	$toasterData = json_decode(file_get_contents(__DIR__ . '/../data/toaster.json'), true);

	$toasterData = array_map(function($x) use($categories) {
		$x['_type'] = 'item';
		$x['shop'] = R::findOne('shop', 'reference = ?', [$x['shopReference']]);

		foreach ($categories as $cat) {
			if ($x['category'] === $cat['name']) {
				$x['category'] = $cat;
				break;
			}
		}

		return $x;
	}, $toasterData);

	$items = R::dispense($toasterData);

	foreach ($items as $item)
		R::storeAll($items);
}
