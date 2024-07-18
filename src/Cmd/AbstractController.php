<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandController;
// Pauldro Minicli
use Pauldro\Minicli\Cli\Printer;
use Pauldro\Minicli\Util\Logger;

/**
 * Class for Handling Executing Commands
 * 
 * @property Call $input
 */
abstract class AbstractController extends CommandController {
	const LOG_CMD_NAME   = 'commands.log';
	const LOG_ERROR_NAME = 'errors.log';
	const OPTIONS = [];
	const OPTIONS_DEFINITIONS = [];
	const REQUIRED_PARAMS = [];

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
	 * @param  string $param
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
	 * Return Path to Log Directory
	 * @return string
	 */
	protected function getLogDir() {
		return rtrim($this->app->config->log_dir, '/') . '/';
	}

	/**
	 * Return the Filepath to the command log
	 * @return string
	 */
	protected function getLogCmdFilePath() {
		return $this->getLogDir() . static::LOG_CMD_NAME;
	}

	/**
	 * Return the Filepath to the error log
	 * @return string
	 */
	protected function getLogErrorFilePath() {
		return $this->getLogDir() . static::LOG_ERROR_NAME;
	}

	/**
	 * Setup Logs Directory
	 * @return bool
	 */
	protected function setupLogDir() {
		if (is_dir($this->getLogDir())) {
			return true;
		}
		return mkdir($this->getLogDir());
	}
	
	/**
	 * Log Command sent to App
	 * @return void
	 */
	protected function logCommand() {
		if (array_key_exists('LOG_COMMANDS', $_ENV) === false || $_ENV['LOG_COMMANDS'] == 'false') {
			return true;
		}

		if ($this->setupLogDir() === false) {
			return false;
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
		if (array_key_exists('LOG_ERRORS', $_ENV) === false || $_ENV['LOG_ERRORS'] == 'false') {
			return true;
		}
		if ($this->setupLogDir() === false) {
			return false;
		}
		$file = $this->getLogErrorFilePath();
		$cmd  = implode(' ', $this->input->getRawArgs());

		$log = Logger::instance();
		$log->log($file, $log->createLogString([$cmd, $msg]));
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
