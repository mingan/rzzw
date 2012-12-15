<?php
if (empty($_GET['slug'])) {
	$out = '[]';
} else {
	$slug = $_GET['slug'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://www4.wiwiss.fu-berlin.de/flickrwrappr/photos/' . $slug . '?format=rdf');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$data = curl_exec($ch);
	$status = curl_getinfo($ch);
	curl_close($ch);

	if ($status['http_code'] != 200) {
		$out = '[]';
	} else {
		$photos = new SimpleXMLElement($data);
		$ns = $photos->getNamespaces(true);
		unset($data);

		$pages = array();
		foreach ($photos->children($ns['rdf']) as $el => $description) {
			if ($el != 'Description') {
				continue;
			}
			foreach ($description->attributes($ns['rdf']) as $attr => $val) {
				if ($attr == 'about') {
					foreach ($description->children($ns['foaf']) as $el => $depiction) {
						$attrs = $depiction->attributes($ns['rdf']);
						$resource = (string)$attrs['resource'];
						if ($el == 'page') {
							$pages[] = array(
								'page' => $resource,
								'thumb' => (string)$val
							);
						}
					}
				}
			}
		}
		$out = json_encode($pages);
	}
}

header('Content-type: application/json');
echo $out;