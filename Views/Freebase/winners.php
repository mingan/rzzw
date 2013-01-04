<?php

$medals = array(
	'gold' => array(),
    'silver' => array(),
    'bronze' => array(),
    'unknown' => array()
);

foreach ($data as $i => $record) {
	$out = '';

	$medalClass = null;
	if (preg_match('#gold|silver|bronze#', strtolower($record["medal"]), $medalClass)) {
		$medalClass = $medalClass[0];
	} else {
		$medalClass = "unknown";
	}

	$out .= '<li class="medal ' . $medalClass . 'Medal"  data-country="' . $record['country'] . '">';
	if (sizeof($record['medalist']) == 1) {
		$out .= '<div><a href="#medalist' . $i . '" data-wiki="'
			. $record['medalist'][0]['key'][0]['value'] . '"><span>'
			. $record['medalist'][0]['name'] . '</span> - '
			. $record['country'] . '</a></div>';
	} else {
		$out .= $record['country'] . '<ul>';
		foreach ($record['medalist'] as $j => $medalist) {
			$out .= '<li><a href="#medalist' . $i . '_' . $j . '" data-wiki="'
				. $medalist['key'][0]['value'] . '"><span>'
				. $medalist['name'] . '</span></a></li>';
		}
		$out .= '</ul>';
	}
	$out .= '</li>';

	$medals[$medalClass][] = $out;
}
foreach ($medals as $m) {
	foreach ($m as $r) {
		echo $r;
	}
}