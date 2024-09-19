<?php namespace Pauldro\Minicli\Cmd\Help;


/**
 * AbstractController
 * Handles Displaying the Help Commands
 */
abstract class AbstractHelpMenuController extends AbstractController  {
	const COMMAND_DEFINITIONS = [
		// '{{cmd}}' => '{{description}}',
	];

	public function handle() {
		$this->init();
		$this->intro();
		$this->display();
	}

	/**
	 * Return Default Display
	 * @return void
	 */
	protected function display() {
		$printer = $this->getPrinter();
		$printer->info('Available Commands:');
		$this->displayCommands();
		$printer->newline();
		$printer->newline();
	}

	/**
	 * Display Commands and their Subcommands
	 * @return void
	 */
	protected function displayCommands() {
		$cmdLength  = $this->getLongestCommandSubcommandLength() + 4;

		foreach ($this->commandMap as $command => $subcommands) {
			if ($command == 'test' || $command == 'help') {
				continue;
			}

			if (is_array($subcommands) === false) {
				$subcommands = [];
			}
			$this->displayCommand($cmdLength, $command, $subcommands);
		}
		$this->displayCommand($cmdLength, 'help', $this->commandMap['help']);
	}

	/**
	 * Display Command Defintion along with subcommands
	 * @param  int    $cmdLength
	 * @param  string $command
	 * @param  array  $subcommands
	 * @return true
	 */
	protected function displayCommand($cmdLength, $command, $subcommands = []) {
		$printer    = $this->getPrinter();
		$this->displayCommandDefinition($cmdLength, $command);

		foreach ($subcommands as $subcommand) {
			if ($subcommand == 'default') {
				continue;
			}
			$this->displayCommandDefinition($cmdLength, $command, $subcommand);
		}
		$printer->newline();
		return true;
	}

	/**
	 * Display Command Defintion
	 * @param  int $cmdLength
	 * @param  string $command
	 * @param  string $subcommand
	 * @return true
	 */
	protected function displayCommandDefinition($cmdLength, $command, $subcommand = 'default') {
		$printer    = $this->getPrinter();
		$handler = $this->getApp()->command_registry->getCallableController($command, $subcommand);
		$printer->newline();

		if ($subcommand == 'default') {
			$line = sprintf('%s%s', $printer->out($this->getCommandToLength($command, $cmdLength), 'info'), $handler::DESCRIPTION);
			$printer->line($line, false);
			return true;
		}
		$cmd = $printer->spaces(2) . $subcommand;
		$line = sprintf('%s%s', $printer->out($this->getCommandToLength($cmd, $cmdLength), 'info'), $handler::DESCRIPTION);
		$printer->line($line, false);
		return true;
	}
	
	/**
	 * Display Intro
	 * @return void
	 */
	protected function intro() {
		$printer = $this->getPrinter();
		$printer->line(static::INTRO_DELIMITER);
		$printer->line('/ ' . $this->getCommandToLength("{$this->app->config->scriptname}:", strlen(static::INTRO_DELIMITER) - 4) . ' /');
		$printer->line('/ ' . $this->getCommandToLength("{$this->app->config->app_description}", strlen(static::INTRO_DELIMITER) - 4) . ' /');
		$printer->line(static::INTRO_DELIMITER);
		$printer->newline();
	}
}
