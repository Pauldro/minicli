<?php namespace Pauldro\Minicli\Cmd\Help;
// PHP Core
use ReflectionClass;
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
	 * Display Command
	 * @return void
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
	 * Display Command Usage
	 * @return void
	 */
	protected function displayUsage() {
		$printer = $this->getPrinter();
		$printer->info('Usage:');
		$printer->line(sprintf('%s%s', $printer->spaces(2), static::COMMAND.' [options]'));
	}

	/**
	 * Display Command Options
	 * @return void
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
	 * Display Command Help
	 * @return void
	 */
	protected function displayHelp() {
		$printer = $this->getPrinter();
		$printer->info('Help:');
		$printer->line(sprintf('%s%s', $printer->spaces(2), static::COMMAND_DEFINITIONS[static::COMMAND]));
	}

	/**
	 * Display Subcommand
	 * @return string
	 */
	protected function displaySubcommand() {
		if (in_array($this->input->lastArg(), static::SUBCOMMANDS)) {
			$reflector = new ReflectionClass(get_class($this));
			$baseNs = $reflector->getNamespaceName();
			$ns = $baseNs . '\\' . ucfirst(static::COMMAND) . '\\';
			$class = $ns . ucfirst($this->input->lastArg()) . 'Controller';
			$handler = new $class();
			$handler->boot($this->app);
			$handler->handle();
			return true;
		}
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
	 * @param  string $cmd	  Command
	 * @param  int	  $length Desired Length
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

	/**
	 * Return String Length of Longest Command
	 * @return int
	 */
	protected function getLongestCommandLength() {
		$length = 0;
		foreach (array_keys(static::COMMAND_DEFINITIONS) as $cmd) {
			if (strlen($cmd) > $length) {
				$length = strlen($cmd);
			}
		}
		return $length;
	}

	/**
	 * Pad Command to Desired String Length
	 * @param  string $cmd	  Command
	 * @param  int	  $length Desired Length
	 * @return string
	 */
	protected function getCommandToLength($cmd, $length) {
		return str_pad($cmd, $length, ' ');
	}

	/**
	 * Return Definition of Command if Definition Exists
	 * @param  string $cmd Command
	 * @return string
	 */
	public function getCommandDefinition($cmd) {
		if (array_key_exists($cmd, static::COMMAND_DEFINITIONS) === false) {
			return '';
		}
		return static::COMMAND_DEFINITIONS[$cmd];
	}
}
