<?php namespace Pauldro\Minicli\Cmd\Help;
// Minicli
use Minicli\App;
use Minicli\Command\CommandController;
// Pauldro\Minicli
use Pauldro\Minicli\Cmd\Controller as ParentController;

/**
 * Handles Displaying the Help Commands
 */
abstract class Controller extends ParentController {
	const COMMAND = '';
	const COMMAND_DEFINITIONS = [];
	const OPTIONS = [];
	const OPTIONS_DEFINITIONS = [];
	const SUBCOMMANDS = [];

	public function handle() {
		$this->display();
	}

	/**
	 * Return Default Display
	 * @return string
	 */
	protected function display() {
		$this->displayUsage();
		$this->displayOptions();
		$this->displayHelp();
		$printer = $this->getPrinter();
		$printer->newline();
		$printer->newline();
	}

	/**
	 * Return Command Usage Display
	 * @return string
	 */
	protected function displayUsage() {
		$printer = $this->getPrinter();
		$printer->info('Usage:');
		$printer->line(sprintf('%s%s', $printer->spaces(2), static::COMMAND.' [options]'));
	}

	/**
	 * Return Command Options Display
	 * @return string
	 */
	protected function displayOptions() {
		$printer = $this->getPrinter();
		$optLength = $this->getLongestOptExampleLength() + 4;
		$printer->info('Options:');

		foreach (static::OPTIONS as $option => $example) {
			$printer->line(sprintf('%s%s%s', $printer->spaces(2), $this->getOptToLength($example, $optLength), $this->getOptDefinition($option)));
		}
	}

	/**
	 * Return Command Help Display
	 * @return string
	 */
	protected function displayHelp() {
		$printer = $this->getPrinter();
		$printer->info('Help:');
		$printer->line(sprintf('%s%s', $printer->spaces(2), static::COMMAND_DEFINITIONS[static::COMMAND]));
	}

	/**
	 * Return String Length of Longest Command
	 * @return int
	 */
	protected function getLongestOptLength() {
		$length = 0;
		foreach (array_keys(static::OPTIONS_DEFINITIONS) as $cmd) {
			if (strlen($cmd) > $length) {
				$length = strlen($cmd);
			}
		}
		return $length;
	}

	/**
	 * Return String Length of Longest Command
	 * @return int
	 */
	protected function getLongestOptExampleLength() {
		$length = 0;
		foreach (array_values(static::OPTIONS) as $cmd) {
			if (strlen($cmd) > $length) {
				$length = strlen($cmd);
			}
		}
		return $length;
	}

	/**
	 * Pad Command to Desired String Length
	 * @param  string $cmd    Command
	 * @param  int    $length Desired Length
	 * @return string
	 */
	protected function getOptToLength($cmd, $length) {
		return str_pad($cmd, $length, ' ');
	}

	/**
	 * Return Argument Defination
	 * @param  string $opt Option, Argument (param|flag)
	 * @return string
	 */
	protected function getOptDefinition($opt) {
		if (array_key_exists($opt, static::OPTIONS_DEFINITIONS) === false) {
			return '';
		}
		return static::OPTIONS_DEFINITIONS[$opt];
	}
}
