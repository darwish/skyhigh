<?php

$replacements = [
	'â€œ' => '“',
	'â€™' => '’',
	'â€˜' => '‘',
	'â€”' => '–',
	'â€“' => '—',
	'â€¢' => '-',
	'â€¦' => '…',
	'â€'  => '”',
];

foreach (glob('data/items/*.json') as $file) {
	$contents = file_get_contents($file);
	$contents = str_replace(array_keys($replacements), array_values($replacements), $contents);
	file_put_contents($file, $contents);
	echo "Fixified $file\n";
}
