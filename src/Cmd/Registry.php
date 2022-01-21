<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandRegistry;

/**
 * Registry
 * Ties Commands to Namespaces by name
 */
class Registry extends CommandRegistry {
	public function registerNamespace($command_namespace) {
		$namespace = new Namespacer($command_namespace);
		$namespace->loadControllers($this->getCommandsPath());
		$this->namespaces[strtolower($command_namespace)] = $namespace;
	}
}
