<?php

namespace Tito10047\BatchSelectionBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tito10047\BatchSelectionBundle\BatchSelectionBundle;
use Tito10047\BatchSelectionBundle\Service\SelectionManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;

class SelectionRuntime implements RuntimeExtensionInterface {

	private string $controllerName = BatchSelectionBundle::STIMULUS_CONTROLLER;

	/**
	 * @param iterable<SelectionManagerInterface> $selectionManagers
	 */
	public function __construct(
		private readonly iterable              $selectionManagers,
		private readonly UrlGeneratorInterface $router
	) {
	}

	public function getStimulusController(string $key, string $manager = 'default'): string {
		$toggleUrl    = $this->router->generate('batch_selection_toggle');
		$selectAllUrl = $this->router->generate('batch_selection_select_all');


		return join(' ', [
			"data-controller=\"{$this->controllerName}\"",
			// URL hodnoty
			"data-{$this->controllerName}-url-toggle-value=\"{$toggleUrl}\"",
			"data-{$this->controllerName}-url-select-all-value=\"{$selectAllUrl}\"",
			// Ostatné hodnoty
			"data-{$this->controllerName}-key-value=\"{$key}\"",
			"data-{$this->controllerName}-manager-value=\"{$manager}\"",
		]);
	}

	public function isSelected(string $key, mixed $item, string $manager = 'default'): bool {
		$manager   = $this->getRowsSelector($manager);
		$selection = $manager->getSelection($key);
		$id        = $selection->normalize($item);
		return $selection->isSelected($id);
	}

	public function rowSelector(string $key, mixed $item, string $manager = 'default'): string {
		$selected  = "";
		$manager   = $this->getRowsSelector($manager);
		$selection = $manager->getSelection($key);

		$id = $selection->normalize($item);
		if ($selection->isSelected($id)) {
			$selected = 'checked="checked" ';
		}

		return "<input type='checkbox' {$selected} name='row-selector[]' class='row-selector' data-{$this->controllerName}-target=\"checkbox\" data-action='{$this->controllerName}#toggle' data-item-id-param='{$id}'>";
	}

	public function rowSelectorAll(string $key, string $manager = 'default'): string {
		$selected = "";

		$manager   = $this->getRowsSelector($manager);
		$selection = $manager->getSelection($key);
		if ($selection->isSelectedAll()) {
			$selected = 'checked="checked" ';
		}

		return "<input type='checkbox' {$selected} name='row-selector-all' class='row-selector' data-{$this->controllerName}-target=\"selectAll\" data-action='{$this->controllerName}#selectAll'>";
	}

	public function getTotal(string $key, string $manager = 'default'): int {
		$manager   = $this->getRowsSelector($manager);
		$selection = $manager->getSelection($key);
		return $selection->getTotal();
	}

	public function getSelectedCount(string $key, string $manager = 'default'): int {
		$manager   = $this->getRowsSelector($manager);
		$selection = $manager->getSelection($key);
		return count($selection->getSelectedIdentifiers());
	}

	private function getRowsSelector(string $manager): SelectionManagerInterface {
		foreach ($this->selectionManagers as $id => $selectionManager) {
			if ($id === $manager) {
				return $selectionManager;
			}
		}
		throw new \InvalidArgumentException(sprintf('No selection manager found for manager "%s".', $manager));
	}
}