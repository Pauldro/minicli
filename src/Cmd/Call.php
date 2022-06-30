<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandCall;

/**
 * Call
 * Parses Command Arguments ($argv) into segments
 * 1. Command
 * 2. Subcommand ** Optional **
 * 3. Parameters
 * 4. Flags (both --[] and -[])
 */
class Call extends CommandCall {
	/**
	 * Return Last Argument sent
	 * @return string
	 */
	public function lastArg() {
		$i = (sizeof($this->args) - 1);
		return array_key_exists($i, $this->args) ? $this->args[$i] : '';
	}

	/**
	 * Parse Command Input
	 * @param  array $argv Input
	 * @return void
	 */
	protected function parseCommand($argv) {
		parent::parseCommand($argv);

		foreach ($argv as $arg) {
			if (strpos($arg, '=') === true) {
				continue;
			}

			if (substr($arg, 0, 2) == '--') {
				continue;
			}

			if (substr($arg, 0, 1) == '-') {
				$this->flags[] = $arg;
				continue;
			}
		}
	}
}
