<?php
namespace Dispatcher;

use Cache\Cache;

class Dispatcher {

	public function dispatch ($params = array()) {
		if ($this->insufficientParams($params)) {
			return false;//@tood nějak vyřešit error
		}
		$params = $this->paramsToCanonical($params);

		if (CACHE_CHECK && Cache::cached($params)) {
			$content = Cache::read($params);
			if (!empty($content)) {
				return $content;
			}
		}

		$fetcherClass = '\Fetchers\\' . $params['source'];
		$Fetcher = new $fetcherClass();
		$data = $Fetcher->fetch(array('type' => $params['type'], 'params' => $params['params']));

		$View = new \Views\View();
		$content = $View->render($params['source'], $params['type'], $data);

		if (CACHE_CHECK) {
			Cache::save($params, $content);
		}

		return $content;
	}

	/**
	 * Checks params and return true if they're insufficient for dispatch
	 * @param $params
	 * @return bool
	 */
	private function insufficientParams ($params) {
		return empty($params['source'])
			|| empty($params['type'])
			|| !isset(Routes::$routes[strtolower($params['source'])]);
	}

	/**
	 * Canonizes params values
	 *
	 * @param array $params
	 * @return array
	 */
	private function paramsToCanonical ($params = array()) {
		$params['source'] = Routes::$routes[strtolower($params['source'])];
		$params['type'] = strtolower($params['type']);
		if (empty($params['params'])) {
			$params['params'] = null;
		}

		return $params;
	}
}
