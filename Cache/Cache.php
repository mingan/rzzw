<?php
namespace Cache;

class Cache {

	/**
	 * Checks for existence of cache, disregards date
	 *
	 * @param array $options
	 * @return bool
	 */
	public static function cached ($options = array()) {
		$key = static::optionsToKey($options);
		return file_exists(static::url($key));
	}

	/**
	 * Tries to save content with timestamp
	 * @param array $options
	 * @param string $content
	 */
	public static function save ($options = array(), $content = '') {
		$key = static::optionsToKey($options);
		file_put_contents(static::url($key), '<!-- Created: ' . date('Y-m-d H:i:s') . ' -->' . $content);
	}

	/**
	 * Reads cache item,  expects file to exists; if cache is stale return empty string
	 *
	 * @param array $options
	 * @return string
	 */
	public static function read ($options = array()) {
		$key = static::optionsToKey($options);
		$content = file_get_contents(static::url($key));
		$head = substr($content, 0, 100);
		if (preg_match('#<!-- Created: (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) -->#',
		               $head,
		               $created)
			&& strtotime($created[1]) >= (time() - 3600 * CACHE_HOUR_LIMIT)) {
			return $content;
		}

		return '';
	}

	/**
	 * Purges cached directory
	 */
	public static function clear () {
		$url = static::url();
		if (file_exists($url)) {
			$handle = opendir($url);

			while (false !== ($file = readdir($handle))) {
				if (is_file($url . $file)) {
					unlink($url . $file);
				}
			}
		}
	}

	/**
	 * Convert options array to simple key
	 *
	 * @param array $options
	 * @return string
	 */
	private static function optionsToKey ($options = array()) {
		$key = $options['source'] . '_' . $options['type'];
		if (!empty($options['params'])) {
			$key .= '_' . md5(serialize($options['params']));
		}
		return $key;
	}

	private static function url ($key = null) {
		$url = BASE . DIRECTORY_SEPARATOR . CACHE_DIR . DIRECTORY_SEPARATOR;
		if (!empty($key)) {
			$url .= $key . '.html';
		}
		return $url;
	}
}
