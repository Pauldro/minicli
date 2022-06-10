<?php namespace Pauldro\Minicli\App;
// Minicli Library
use Minicli\App as MinicliApp;
// Pauldro Minicli
use Pauldro\Minicli\Cli\Printer;
use Pauldro\Minicli\Cmd;

class App extends MinicliApp {

	 public function __construct(array $config = null) {
		parent::__construct($this->parseConfig($config));

		$this->addService('printer', Printer::getInstance());
		$this->addService('command_registry', new Cmd\Registry($this->config->app_path));
	}

	public function parseConfig(array $config = null) {
		return array_merge([
			'app_path' => __DIR__ . '/../app/Cmd',
		], $config);
	}

	public function runCommand(array $argv = []) {
		$input = new Cmd\Call($argv);

		if (count($input->args) < 2) {
			$this->printSignature();
			exit;
		}

		$controller = $this->command_registry->getCallableController($input->command, $input->subcommand);

		if (empty($controller)) {
			$cmd = $input->command;

			if (strtolower($input->subcommand) == 'default') {
				$cmd .= " $input->subcommand";
			}
			Printer::getInstance()->error("Controller not found for $cmd");
			exit;
		}

		if ($controller instanceof Cmd\Controller) {
			$controller->boot($this);
			$controller->run($input);
			$controller->teardown();
			exit;
		}
		$this->runSingle($input);
	}
}
