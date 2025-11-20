<?php

namespace Tito10047\BatchSelectionBundle\Service;

interface IdentityResolverInterface {

	/**
	 * Converts the item into a scalar identifier.
	 *
	 * @param mixed $item The object or value to convert.
	 * @param string $identifierPath The property path to use if required (e.g., 'uuid' instead of 'id').
	 * @return string|int The scalar identifier.
	 * @throws \RuntimeException If normalization fails.
	 */
	public function normalize(mixed $item, string $identifierPath): string|int;
}