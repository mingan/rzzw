<?php
namespace Fetchers;

/*
 * Fetches RDF data from NYTimes
 */
class NytimesData extends Fetcher {

	/**
	 * Public method for fetching different types of data with predefined queries
	 *
	 * @param array $options type and mid
	 * @return mixed|null
	 */
	public function fetch ($options = array()) {
		static::$queryUrl = $options['params'] . '.json';

		$result = $this->request();

		return array(
			'redirect' => array(
				'source' => 'nytsearch',
				'type' => 'articles',
				'params' => $result
			)
		);
	}

	protected function parseResponse ($data) {
		$data = preg_replace('#/\*.+?\*/#sui', '', $data);
		$data = json_decode($data, true);

		$key = null;
		$keys = array_keys($data);
		foreach ($keys as $k) {
			if (!in_array($k, array('namepace', 'namespace')) && !preg_match('#\.rdf$#', $k)) {
				$key = $k;
				break;
			}
		}

		if (empty($key)) {
			return array();
		}

		return $data[$key]['skos:prefLabel'][0];
	}
}