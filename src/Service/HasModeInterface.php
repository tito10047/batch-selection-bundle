<?php

namespace Tito10047\BatchSelectionBundle\Service;

use Tito10047\BatchSelectionBundle\Enum\SelectionMode;

interface HasModeInterface {
	public function setMode(SelectionMode $mode): void;
	public function getMode(): SelectionMode;
}