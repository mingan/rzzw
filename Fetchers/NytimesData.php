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

		return array(
			'articles' => $data[$key]['nyt:associated_article_count'][0],
			'name' => $data[$key]['skos:prefLabel'][0]
		);
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
			'PREFIX owl: <http://www.w3.org/2002/07/owl#> ' .

			'select ?abstract, ?img, ?name, ?nyt where {' .
				'{' .
					$dbpUrl . ' a foaf:Person;' .
						'foaf:name ?name;' .
						'dbo:abstract ?abstract . ' .
					'OPTIONAL {' . $dbpUrl . ' foaf:depiction ?img }' .
				    'OPTIONAL {?nyt owl:sameAs ' . $dbpUrl . ' FILTER regex(?nyt, \'http://data\\\.nytimes\\\.com/.*\')}' .
				'} UNION {' .
					$dbpUrl . ' dbo:wikiPageRedirects ?u .' .
					'?u a foaf:Person;' .
						'foaf:name ?name;' .
						'dbo:abstract ?abstract . ' .
					'OPTIONAL { ?u foaf:depiction ?img } ' .
					'OPTIONAL { ?nyt owl:sameAs ?u FILTER regex(?nyt, \'http://data\\\.nytimes\\\.com/.*\')} ' .
				'}' .
				'FILTER langMatches( lang(?abstract), "EN" )' .
			'} LIMIT 1'; // @todo víc?

		return $query;
	}
}