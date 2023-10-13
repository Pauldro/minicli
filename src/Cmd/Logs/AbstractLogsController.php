<?php namespace Pauldro\Minicli\Cmd\Logs;
// Pauldro Minicli
use Pauldro\Minicli\Util\Logger;
use Pauldro\Minicli\Cmd\AbstractController;


/**
 * AbstractLogsController
 * Base Controller Class for Interacting with Logs
 * 
 * Usage:
 *   [shell] [argument] [options]
 * Options:
 */
abstract class AbstractLogsController extends AbstractController {
	const LOGS = ['commands', 'error'];


/* =============================================================
	Init Functions
============================================================= */
	/**
	 * Initialize App
	 * @return bool
	 */
	protected function init() {
		$this->logCommand();

		if ($this->initRequiredParams() === false) {
			return false;
		}
		return true;
	}

	/**
	 * Validates that Required Params are set
	 * @return bool
	 */
	protected function initRequiredParams() {
		if ($this->hasParam('log') === false) {
			return $this->error("Please provide log name (log=LOG)");
		}
		if (in_array(rtrim($this->getParam('log'), '.log'), static::LOGS) === false) {
			return $this->error("Log not found: " . $this->getParam('log'));
		}
		return true;
	}

/* =============================================================
	Logging Functions
============================================================= */
	/**
	 * Return the Filepath to the log
	 * @param  string $log  Log Name
	 * @return string
	 */
	protected function getLogFilePath($log) {
		return $this->app->config->log_dir . "/$log.log";
	}

	/**
	 * Display Last Line of Log
	 * @param  string $log
	 * @return bool
	 */
	protected function last($log) {
		if (is_file($this->getLogFilePath($log)) == false) {
			return $this->error("Log file not found");
		}
		$cmd = 'tail -n 1 ' . $this->getLogFilePath($log);
		$line = exec($cmd);
		$this->getPrinter()->info("Tail: $line");
		return true;
	}

	/**
	 * Clear Log file
	 * @param  string $log
	 * @return bool
	 */
	protected function clear($name) {
		$this->getPrinter()->info($this->getLogFilePath($name));
		$log = Logger::instance();
		if ($log->clear($this->getLogFilePath($name))) {
			return $this->error("Failed to clear log: $name");
		}
		return true;
	}
}
