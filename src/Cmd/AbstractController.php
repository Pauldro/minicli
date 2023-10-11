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
	 * @return Printer
	 */
	protected function getPrinter() {
		return $this->getApp()->getPrinter();
	}

	/**
	 * @param $param
	 * @return string
	 */
	protected function getParam($param) {
		return $this->input->getParam($param);
	}
}
