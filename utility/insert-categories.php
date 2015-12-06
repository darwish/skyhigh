<?php
require __DIR__ . '/../includes/start.php';

$categories = json_decode(file_get_contents(__DIR__ . "/../data/categories.json"), true);

foreach ($categories as $parentName => $children) {
	$parent = storeCategory($parentName);

	foreach ($children as $childName) {
		storeCategory($childName, $parent);
	}
}

function storeCategory($name, \RedBeanPHP\OODBBean $parent = null) {
	$category = R::dispense("category");
	$category->name = $name;
	if ($parent) {
		$category->category = $parent;
	}
	R::store($category);
	return $category;
}