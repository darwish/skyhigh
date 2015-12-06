<?php

function generateDB() {
	R::exec('DROP TABLE category');
	R::exec('DROP TABLE item');
	R::exec('DROP TABLE shop');
	
	require __DIR__ . '/../utility/insert-categories.php';
	
	$storeData = json_decode(file_get_contents(__DIR__ . '/../data/stores.json'), true);

	$categories = R::findAll("category");
	
	foreach ($storeData as $store) {
		$store['_type'] = 'shop';
		$storeBean = R::dispense($store);
		R::store($storeBean);
	}
	
	if ($handle = opendir(__DIR__ . '/../data/items/')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				addItemToDB($entry, $categories);
			}
		}
	
		closedir($handle);
	}
}

function addItemToDB($filename, $categories) {
	
	$itemData = json_decode(file_get_contents(__DIR__ . "/../data/items/$filename"), true);
	
	$itemData = array_map(function($x) use($categories) {
		$x['_type'] = 'item';
		$x['shop'] = R::findOne('shop', 'reference = ?', [$x['shopReference']]);
		unset($x['shopReference']);

		foreach ($categories as $cat) {
			if (strtolower($x['category']) === strtolower($cat['name'])) {
				$x['category'] = $cat;
				break;
			}
		}

		return $x;
	}, $itemData);

	$items = R::dispense($itemData);

	foreach ($items as $item)
		R::storeAll($items);
}
