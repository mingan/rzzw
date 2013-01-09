<?php
namespace Fetchers;

/*
 * Fetches data from NYTimes
 */
class NytimesSearch extends Fetcher {
	protected static $queryUrl = 'http://api.nytimes.com/svc/search/v1/article';
	protected static $apiKey = '717cb0a3922dd2e28ba3fb86fb19b2f9:8:67161252';

	/**
	 * Public method for fetching different types of data with predefined queries
	 *
	 * @param array $options type and mid
	 * @return mixed|null
	 */
	public function fetch ($options = array()) {
		if (isset($options['params'])) {
			$options = $options['params'];
		}
		$default = array(
			'articles' => 10,
			'name' => null
		);
		$options += $default;

		$params = array(
			'api-key=' . static::$apiKey,
			'query=' .  urlencode($options['name']),
			'fields=title,body,url,date,small_image_url,small_image_height,small_image_width'
		);

		$params = implode('&', $params);

		return $this->returnData($this->request($params));
	}

	protected function parseResponse ($data) {

		if (empty($data)) {
			return array();
		}
		$data = json_decode($data, true);

		return $data['results'];
	}
}