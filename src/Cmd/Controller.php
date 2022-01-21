<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandController;
// Printer
use Lib\Cli\Printer;

/**
 * Base Class for Handling Executing Commands
 */
abstract class Controller extends CommandController {
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
}
