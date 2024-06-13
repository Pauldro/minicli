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
		$printer = $this->getPrinter();
		$cmdLength = $this->getLongestCommandLength() + 4;

		foreach ($this->getApp()->command_registry->getCommandMap() as $command => $sub) {
			$printer->newline();
			$printer->line(sprintf('%s%s', $printer->out($this->getCommandToLength($command, $cmdLength), 'info'), $this->getCommandDefinition($command)));

			if (is_array($sub)) {
				foreach ($sub as $subcommand) {
					if ($subcommand !== 'default') {
						$printer->line(sprintf('%s%s', $printer->spaces(2), $subcommand));
					}
				}
			}
		}
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
