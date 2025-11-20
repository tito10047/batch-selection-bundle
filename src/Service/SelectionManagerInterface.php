<?php

namespace Tito10047\BatchSelectionBundle\Service;

interface SelectionManagerInterface {

	public function registerSource(string $key, mixed $source): static;

	public function clear(string $key): static;

	public function isSelected(string $key, mixed $item): bool;
}