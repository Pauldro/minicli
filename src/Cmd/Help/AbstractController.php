<?php namespace Pauldro\Minicli\Cmd\Help;
// PHP Core
use ReflectionClass;
// Pauldro\Minicli
use Pauldro\Minicli\Cmd\AbstractController as ParentController;

/**
 * AbstractController
 * Handles Displaying the Help Commands
 * 
 * @property array $commandMap Array of Map Commands
 */
abstract class AbstractController extends ParentController {
	const COMMAND = '';
	const DESCRIPTION = '';
	const COMMAND_DEFINITIONS = [];
	const OPTIONS = [];
	const OPTIONS_DEFINITIONS = [];
	const OPTIONS_DEFINITIONS_OVERRIDE = [];
	const SUBCOMMANDS = [];
	const NOTES = [];
	const INTRO_DELIMITER = '/////////////////////////////////////////////////////////';
	const REQUIRED_PARAMS = [];

	protected $commandMap = [];

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
		$this->displayRequiredParams();
		$this->displayHelp();
		$this->displaySubcommands();
		$this->displayNotes();

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
		$printer->line(sprintf('%s%s%s', $this->app->getSignature(), $printer->spaces(1), static::COMMAND . ' [options]'));
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
	 * Display Required Command Parameters
	 * @return void
	 */
	protected function displayRequiredParams() {
		if (empty(static::REQUIRED_PARAMS)) {
			return true;
		}

		$printer = $this->getPrinter();
		$optLength = $this->getLongestOptExampleLength() + 4;
		$printer->info('Required:');

		foreach (static::REQUIRED_PARAMS as $option) {
			if (array_key_exists($option, static::REQUIRED_PARAMS) === false) {
				continue;
			}
			$example = static::OPTIONS[$option];
			$printer->line(sprintf('%s%s%s', $printer->spaces(2), $this->getOptToLength($example, $optLength), $this->getOptDefinition($option)));
		}
	}

	/**
	 * Display Subcommands
	 * @return void
	 */
	protected function displaySubcommands() {
		$printer = $this->getPrinter();

		if (empty(static::SUBCOMMANDS) === false) {
			$printer->info('See Also:');
		}

		foreach (static::SUBCOMMANDS as $cmd) {
			$printer->line(sprintf('%s%s%s%s%s', $printer->spaces(2), 'help ', static::COMMAND, ' ', $cmd));
		}
	}

	/**
	 * Display Notes
	 * @return void
	 */
	protected function displayNotes() {
		$printer = $this->getPrinter();

		if (empty(static::NOTES) === false) {
			$printer->info('Notes:');
		}

		foreach (static::NOTES as $line) {
			$printer->line(sprintf('%s%s%s', $printer->spaces(2), ' ', $line));
		}
	}

	/**
	 * Display Command Help
	 * @return void
	 */
	protected function displayHelp() {
		$printer = $this->getPrinter();
		$printer->info('Help:');
		$printer->line(sprintf('%s%s', $printer->spaces(2), static::DESCRIPTION));
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
		if (array_key_exists($opt, static::OPTIONS_DEFINITIONS_OVERRIDE)) {
			return static::OPTIONS_DEFINITIONS_OVERRIDE[$opt];
		}
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
	 * Return the Longest Command / Subcommand length
	 * @return int
	 */
	protected function getLongestCommandSubcommandLength() {
		$length = 0;

		foreach ($this->commandMap as $command => $subcommands) {
			if (strlen($command) > $length) {
				$length = strlen($command);
			}
			if (is_array($subcommands)) {
				foreach ($subcommands as $subcommand) {
					if ($subcommand == 'default') {
						continue;
					}
					$cmd =  '  ' . $subcommand;

					if (strlen($cmd) > $length) {
						$length = strlen($cmd);
					}
				}
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

/* =============================================================
	Init Functions
============================================================= */
	/**
	 * Initialize App
	 * @return bool
	 */
	protected function init() {
		return $this->initCommandMap();

	}

	/**
	 * Initialize Command Map
	 * @return bool
	 */
	protected function initCommandMap() {
		$this->commandMap = $this->getApp()->command_registry->getCommandMap();
		return true;
	}
}
