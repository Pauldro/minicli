<?php namespace Pauldro\Minicli\Cmd;
// Base PHP
use ReflectionClass;

/**
 * Namespacer
 * Ties Controller to a Command
 */
class Namespacer {
	protected $name;
	protected $controllers = [];

	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Return Name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Load Controllers
	 * @param  string $commands_path
	 * @return array
	 */
	public function loadControllers($commands_path) {
		foreach (glob($commands_path . '/' . $this->getName() . '/*Controller.php') as $controller_file) {
			$this->loadCommandMap($controller_file);
		}

		return $this->getControllers();
	}

	/**
	 * Return Controllers
	 * @return array
	 */
	public function getControllers() {
		return $this->controllers;
	}

	/**
	 * Return Controller Class for Command
	 * @param  string $command_name
	 * @return string
	 */
	public function getController($command_name) {
		return isset($this->controllers[$command_name]) ? $this->controllers[$command_name] : null;
	}

	/**
	 * Map Controllers for each command
	 * @param  string $controller_file
	 * @return void
	 */
	protected function loadCommandMap($controller_file) {
		$filename = basename($controller_file);

		$controller_class = str_replace('.php', '', $filename);
		$command_name = strtolower(str_replace('Controller', '', $controller_class));
		$full_class_name = sprintf('App\\Cmd\\%s\\%s', $this->getName(), $controller_class);

		$reflector = new ReflectionClass($full_class_name);
		
		if ($reflector->isAbstract()) {
			return false;
		}

		/** @var Controller $controller */
		$controller = new $full_class_name();
		$this->controllers[$command_name] = $controller;
	}
}
