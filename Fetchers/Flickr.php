<?php
namespace Fetchers;

/*
 * Fetches photos form flickr wrapper
 */
class Flickr extends Fetcher {
	protected static $queryUrl = 'http://www4.wiwiss.fu-berlin.de/flickrwrappr/photos/';

	/**
	 * Public method for fetching different types of data with predefined queries
	 *
	 * @param array $options type and mid
	 * @return mixed|null
	 */
	public function fetch ($options = array()) {
		if (isset($options['params'])) {
			$options['ident'] = $options['params'];
		}
		$default = array(
			'ident' => null
		);
		$options += $default;
		static::$queryUrl .= $options['ident'];

		$params = 'format=rdf';

		return $this->request($params, 'json');
	}

	protected function parseResponse ($data) {
		if (empty($data)) {
			return array();
		}
		$photos = new \SimpleXMLElement($data);
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
		return $pages;
	}
}