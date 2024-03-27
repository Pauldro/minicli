<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandController;
// Printer
use Lib\Cli\Printer;

/**
 * Class for Handling Executing Commands
 * 
 * @property Call $input
 */
abstract class AbstractController extends CommandController {
	/**
	 * Return boolean value for parameter
	 * @param  string $param Parameter to get Value from
	 * @return bool
	 */
	protected function getParamBool($param) {
		$value = $this->input->getParam($param);
		if (empty($value)) {
			return false;
		}
		return strtolower($value) == 'y' || strtolower($value) == 'true';
	}

	/**
	 * Return Parameter Value
	 * @param $param
	 * @return string
	 */
	protected function getParam($param) {
		return $this->input->getParam($param);
	}

	/**
	 * Return Parameter Value as array
	 * @param  string $param      Parameter Key
	 * @param  string $delimeter  Delimiter
	 * @return array
	 */
	protected function getParamArray($param, $delimeter = ",") {
		return explode($delimeter, $this->getParam($param));
	}

	/**
	 * @return Printer
	 */
	protected function getPrinter() {
		return $this->getApp()->getPrinter();
	}

	/**
	 * @return Printer
	 */
	protected function printer() {
		return $this->getApp()->getPrinter();
	}

/* =============================================================
	Init Functions
============================================================= */
	/**
	 * Initialize App
	 * @return bool
	 */
	protected function init() {
		return true;
	}
/* =============================================================
	Logging Functions
============================================================= */
	/**
	 * Return the Filepath to the command log
	 * @return string
	 */
	protected function getLogCmdFilePath() {
		return $this->app->config->log_dir . '/' . static::LOG_CMD_NAME;
	}

	/**
	 * Return the Filepath to the error log
	 * @return string
	 */
	protected function getLogErrorFilePath() {
		return $this->app->config->log_dir . '/' . static::LOG_ERROR_NAME;
	}
	
	/**
	 * Log Command sent to App
	 * @return void
	 */
	protected function logCommand() {
		if (array_key_exists('LOG_COMMANDS', $_ENV) === false || boolval($_ENV['LOG_COMMANDS']) === false) {
			return true;
		}
		$file = $this->getLogCmdFilePath();
		$cmd  = implode(' ', $this->input->getRawArgs());

		$log = Logger::instance();
		$log->log($file, $cmd);
	}

	/**
	 * Log Command sent to App
	 * @return void
	 */
	protected function logError($msg) {
		if (array_key_exists('LOG_ERRORS', $_ENV) === false || boolval($_ENV['LOG_ERRORS']) === false) {
			return true;
		}
		$file = $this->getLogErrorFilePath();
		$cmd  = implode(' ', $this->input->getRawArgs());

		$log = Logger::instance();
		$log->log($file, $cmd);
	}

	/**
	 * Log Error Message
	 * @param  string $msg
	 * @return false
	 */
	protected function error($msg) {
		$this->getPrinter()->error($msg);
		$this->logError($msg);
		return false;
	}
}
