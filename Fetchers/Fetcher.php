<?php
namespace Fetchers;

/**
 * General fetcher of data from 3rd party services
 */
abstract class Fetcher {

	/**
	 * @var URL of service
	 */
	protected static $queryUrl;


	protected static $mimeTypes = array(
		'json' => 'application/json',
		'xml' => ''
	);

	/**
	 * Calls service with curl, fetches response and decodes it
	 *
	 * @param string $urlParams
	 * @param string $format MIME type of expected response
	 * @return mixed|null
	 */
	protected function request ($urlParams = '', $format = null) {
		$headers = array();
		if (!empty($format) && !empty(static::$mimeTypes[$format])) {
			$headers[] = 'Accept: ' . static::$mimeTypes[$format];
		}

		$handle = curl_init(static::$queryUrl . '?' . $urlParams);

		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		$data = curl_exec($handle);

		$error = curl_errno($handle);
		if ($error > 0) {
			return null;
		}

		$data = $this->parseResponse($data);
		return $data;
	}

	/**
	 * Default decode implementation only calls json_decode
	 *
	 * @param $data
	 * @return mixed
	 */
	protected function parseResponse ($data) {
		$data = json_decode($data, true);

		return $data;
	}

	/**
	 * Public method of each Fetcher class
	 *
	 * @param array $options
	 * @return mixed
	 */
	abstract public function fetch ($options = array());
}