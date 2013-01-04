<?php
namespace Views;

class View {

	public function render ($source = '', $type = '', $data = array()) {

		ob_start();
		require __DIR__
			. DIRECTORY_SEPARATOR . $source
			. DIRECTORY_SEPARATOR . $type . '.php';
		$output = ob_get_clean();

		return $output;
	}

}
