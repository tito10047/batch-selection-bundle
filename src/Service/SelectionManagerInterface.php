<?php

namespace Tito10047\BatchSelectionBundle\Service;

interface SelectionManagerInterface {

	public function registerSource(string $key, mixed $source, string $type, ?string $identifierPath = null): SelectionInterface;

	public function getSelection(string $key, string $type, ?string $identifierPath = null): SelectionInterface;

}