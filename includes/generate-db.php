<?php
//require_once __DIR__ . '/rb.php';

function generateDB() {
	$storeData = json_decode(file_get_contents(__DIR__ . '/../data/stores.json'), true);
	//pr($storeData);die;
	
	foreach ($storeData as $store) {
		$store['_type'] = 'shop';
		pr($store);
		$storeBean = R::dispense($store);
		R::store($storeBean);
	}
	
	$toasterData = json_decode(file_get_contents(__DIR__ . '/../data/toaster.json'), true);
	
	$toasterData = array_map(function($x) { 
		$x['_type'] = 'item';
		$x['shop_id'] = R::findOne('shop', 'reference = ?', [$x['shopReference']]);
		return $x; 
	}, $toasterData);
	
	$items = R::dispense($toasterData);
	
	foreach ($items as $item)	
		R::storeAll($items);
}

//generateDB();