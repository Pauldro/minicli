<?php namespace Pauldro\Minicli\Util;

/**
 * Logger
 * 
 * Updates Log Files
 */
class Logger {
	private static $instance;

	/**
	 * Return instance
	 * @return Logger
	 */
	public static function instance() {
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Record Log Message
	 * @param  string $file  /path/to/file/
	 * @param  string $text
	 * @return bool
	 */
	public function log($file, $text) {
		$content = '';

		if (file_exists($file)) {
			$content = file_get_contents($file);
		}

		$line = implode("\t", [date('Ymd'), date('His'), $text]) . PHP_EOL;
		return file_put_contents($file, $content . $line);
	}
}