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
	const NOTES = [];
	const OPTIONS_DEFINITIONS = [];
	const OPTIONS_DEFINITIONS_OVERRIDE = [];
	const REQUIRED_PARAMS = [];
	const SENSITIVE_PARAM_VALUES = [];

/* =============================================================
	Parameter Functions
============================================================= */
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

/* =============================================================
	Printer
============================================================= */
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
		$this->initEnvTimeZone();
		
		if ($this->initRequiredParams() === false) {
			return false;
		}
		return true;
	}

	/**
	 * Initialize the Local Time Zone
	 * @return bool
	 */
	protected function initEnvTimeZone() {
		$sysTZ = exec('date +%Z');
		$abbr = timezone_name_from_abbr($sysTZ);
		return date_default_timezone_set($abbr);
	}

	/**
	 * Initialize App
	 * @return bool
	 */
	protected function initRequiredParams() {
		foreach (static::REQUIRED_PARAMS as $param) {
			if ($this->hasParam($param) === false) {
				$description = array_key_exists($param, static::OPTIONS_DEFINITIONS) ? static::OPTIONS_DEFINITIONS[$param] : $param;
				$use         = array_key_exists($param, static::OPTIONS) ? static::OPTIONS[$param] : '';
				return $this->error("Missing Parameter: $description ($use)");
			}
		}
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
	 * Sanitize Command for Log Use
	 * @return string
	 */
	protected function sanitizeCmdForLog() {
		$cmd = implode(' ', $this->input->getRawArgs());

		foreach (static::SENSITIVE_PARAM_VALUES as $param) {
			$find = "$param=" . $this->getParam($param);
			$cmd = str_replace($find, "$param=***", $cmd);
		}
		return $cmd;
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
		$cmd  = $this->sanitizeCmdForLog();

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
		$cmd  = $this->sanitizeCmdForLog();

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

	/**
	 * Display Success Message
	 * @param  string $msg
	 * @return true
	 */
	protected function success($msg) {
		if ($this->hasFlag('--debug')) {
			$this->printer()->success("Success: $msg");
			return true;
		}
		$this->printer()->success($msg);
		return true;
	}
}
