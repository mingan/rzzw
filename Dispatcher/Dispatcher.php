<?php
namespace Dispatcher;

class Dispatcher {

	public function dispatch ($params = array()) {
		if ($this->insufficientParams($params)) {
			return false;//@tood nějak vyřešit error
		}
		extract($params);

		$source = Routes::$routes[strtolower($source)];
		$type = strtolower($type);

		$fetcherClass = '\Fetchers\\' . $source;
		$Fetcher = new $fetcherClass();
		$data = $Fetcher->fetch(array('type' => $type));

		$View = new \Views\View();
		return $View->render($source, $type, $data);
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
