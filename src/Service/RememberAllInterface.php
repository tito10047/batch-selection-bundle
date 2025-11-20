<?php

namespace Tito10047\BatchSelectionBundle\Service;

interface RememberAllInterface {

	/**
	 * @param array<int|string> $ids
	 *
	 * @return $this
	 */
	public function rememberAll(array $ids):static;

}