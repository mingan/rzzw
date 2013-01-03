<?php
namespace Dispatcher;

class Dispatcher {

	public function dispatch ($params = array()) {
		if ($this->insufficientParams($params)) {
			return false;//@tood nějak vyřešit error
		}
		extract($params);

		$fetcherClass = 'Fetchers\\' . Routes::$routes[strtolower($source)];
		$Fetcher = new $fetcherClass();
		$data = $Fetcher->fetch(array('type' => $type));
		return $data;
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
}
