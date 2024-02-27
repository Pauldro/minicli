<?php namespace Pauldro\Minicli\App;
// Minicli Library
use Minicli\App as MinicliApp;
// Pauldro Minicli
use Pauldro\Minicli\Cli\Printer;
use Pauldro\Minicli\Cmd;

class App extends MinicliApp {

	 public function __construct(array $config = null) {
		parent::__construct($this->parseConfig($config));

		$this->addServices();
	}

	/**
	 * Add Services (printer, command_registry)
	 * @return void
	 */
	protected function addServices() {
		$this->addService('printer', Printer::getInstance());

		$reg = new Cmd\Registry($this->config->app_path);
		$reg->setAppNamespace($this->config->app_namespace);
		$this->addService('command_registry', $reg);
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

		try {
			$controller = $this->command_registry->getCallableController($input->command, $input->subcommand);
		} catch (\ReflectionException $e) {
			$controller = null;
		}

		if (empty($controller)) {
			$cmd = $input->command;

			if (strtolower($input->subcommand) == 'default') {
				$cmd .= " $input->subcommand";
			}
			Printer::getInstance()->error("Controller not found for $cmd");
			exit;
		}

		if ($controller instanceof Cmd\AbstractController) {
			$controller->boot($this);
			$controller->run($input);
			$controller->teardown();
			exit;
		}
		$this->runSingle($input);
	}
}
