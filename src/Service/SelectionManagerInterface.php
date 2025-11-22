<?php

namespace Tito10047\BatchSelectionBundle\Service;

use Tito10047\BatchSelectionBundle\Normalizer\IdentifierNormalizerInterface;

interface SelectionManagerInterface {

	public function registerSource(string $key, mixed $source, ?IdentifierNormalizerInterface $normalizer = null): SelectionInterface;

	public function getSelection(string $key): SelectionInterface;

}