<?php
namespace Fetchers;

/*
 * Fetches query from DBPedia via SPARQL endpoint
 */
class Dbpedia extends Fetcher {
	protected static $queryUrl = 'http://dbpedia.org/sparql';

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

		$params = array(
			'query=' . urlencode($this->constructQuery($options['ident'])),
			'default-graph-uri=' . urlencode('http://dbpedia.org')
		);
		$params = implode('&', $params);

		return $this->request($params, 'json');
	}

	protected function parseResponse ($data) {
		$data = json_decode($data, true);

		if (empty($data) || empty($data['results']['bindings'])) {
			return array();
		} else if (isset($data['results'])) {
			$data = $data['results']['bindings'][0];
			$return = array();
			foreach ($data as $field => $v) {
				$return[$field] = $v['value'];
			}

			return $return;
		}

		return $data;
	}

	/**
	 * Get SPARQL query
	 *
	 * @param mixed $ident String ident in Wikipedia/DBPedia
	 * @return string
	 */
	private function constructQuery ($ident = null) {
		if (empty($ident)) {
			return '';
		}

		$dbpUrl = '<http://dbpedia.org/resource/' . $ident . '>';
		$query =
			'PREFIX foaf: <http://xmlns.com/foaf/0.1/> ' .
			'PREFIX dbo: <http://dbpedia.org/ontology/> ' .

			'select ?abstract, ?img, ?name where {' .
				'{' .
					$dbpUrl . ' a foaf:Person;' .
						'foaf:name ?name;' .
						'dbo:abstract ?abstract . ' .
					'OPTIONAL {' . $dbpUrl . ' foaf:depiction ?img} ' .
				'} UNION {' .
					$dbpUrl . ' dbo:wikiPageRedirects ?u .' .
					'?u a foaf:Person;' .
						'foaf:name ?name;' .
						'dbo:abstract ?abstract . ' .
					'OPTIONAL {?u foaf:depiction ?img} ' .
				'}' .
				'FILTER langMatches( lang(?abstract), "EN" )' .
			'} LIMIT 1'; // @todo víc?

		return $query;
	}
}