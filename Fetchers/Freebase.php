<?php
namespace Fetchers;

/*
 * Fetches query from Freebase via MQL
 */
class Freebase extends Fetcher {
	protected static $queryUrl = 'https://www.googleapis.com/freebase/v1/mqlread';
	private static $apiKey = 'AIzaSyAaaooRwzP5tfuBZ6k95ro8RWlt1MLwpn8';

	/**
	 * Public method for fetching different types of data with predefined queries
	 *
	 * @param array $options type and mid
	 * @return mixed|null
	 */
	public function fetch ($options = array()) {
		if (isset($options['params'])) {
			$options['mid'] = $options['params'];
		}
		$default = array(
			'type' => 'games',
			'mid' => null
		);
		$options += $default;

		$params = array(
			'key=' . static::$apiKey,
			'query=' . $this->constructQuery($options['type'], $options['mid'])
		);
		$params = preg_replace('#\s+#', '', implode('&', $params));

		return $this->returnData($this->request($params));
	}

	protected function parseResponse ($data) {
		$data = json_decode($data, true);
		if (empty($data)) {
			return array();
		} else if (isset($data['result'])) {
			return $data['result'];
		}

		return $data;
	}

	/**
	 * Get MQL query string of given type with MID if necessary
	 *
	 * @param string $type games | events | disciplines | winners
	 * @param mixed $mid null or Freebase MID as a string
	 * @return string
	 */
	private function constructQuery ($type = '', $mid = null) {
		$queries = array(
			'games' => '[{
	            "type" : "/olympics/olympic_games",
	            "name" : null,
	            "/time/event/start_date<=" : "' . date('Y-m-d') . '",
	            "/time/event/start_date" : null,
	            "sort" : ["-/time/event/start_date", "-name"],
	            "host_city" : null,
	            "mid" : null
	        }]',
			'events' => '[{
	            "!pd:/time/event/includes_event": [
	                {
	                    "!index": null,
	                    "mid": "' . $mid . '",
	                    "type": "/time/event"
	                }
	            ],
	            "mid": null,
	            "name": null,
	            "sort": "!pd:/time/event/includes_event.!index",
	            "type": "/time/event"
	        }]',
			'disciplines' => '[{
	            "!pd:/time/event/includes_event": [
	                {
	                    "!index": null,
	                    "mid": "' . $mid . '",
	                    "type": "/time/event"
	                }
	            ],
	            "mid": null,
	            "name": null,
	            "sort": "!pd:/time/event/includes_event.!index",
	            "type": "/time/event"
	        }]',
			'winners' => '[{
	            "type" : "/olympics/olympic_medal_honor",
	            "event" : {
	                "mid" : "' . $mid . '"
	            },
	            "limit" : 10,
	            "medal" : null,
	            "medalist" : [{
	                "name" : null,
	                "key" : [{
	                    "namespace" : "/wikipedia/en",
	                    "value" : null
	                }]
	            }],
                "country" : null
	        }]'
		);

		if (empty($queries[$type]) || ($type != 'games' && empty($mid))) {
			return '';
		}

		return $queries[$type];
	}
}