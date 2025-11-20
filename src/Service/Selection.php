<?php

namespace Tito10047\BatchSelectionBundle\Service;

use Tito10047\BatchSelectionBundle\Enum\SelectionMode;
use Tito10047\BatchSelectionBundle\Normalizer\IdentifierNormalizerInterface;
use Tito10047\BatchSelectionBundle\Storage\StorageInterface;

class Selection implements SelectionInterface, RememberAllInterface, HasModeInterface {

	public function __construct(
		private readonly string                        $key,
		private readonly ?string                       $identifierPath,
		private readonly StorageInterface              $storage,
		private readonly IdentifierNormalizerInterface $normalizer
	) {
	}

	public function clearSelected(): static {
		$this->storage->clear($this->key);
		return $this;
	}

	public function isSelected(mixed $item): bool {
		return $this->storage->hasIdentifier($this->key, $item);
	}

	public function select(mixed $item): static {
		$id = $this->normalizer->normalize($item, $this->identifierPath);
		$this->storage->add($this->key, [$id]);
		return $this;
	}

	public function unselect(mixed $item): static {
		$id = $this->normalizer->normalize($item, $this->identifierPath);
		$this->storage->remove($this->key, [$id]);
		return $this;
	}

	public function selectMultiple(array $items): static {
		$ids = [];
		foreach ($items as $item) {
			$ids[] = $this->normalizer->normalize($item, $this->identifierPath);
		}
		$this->storage->add($this->key, $ids);
		return $this;
	}

	public function getSelectedIdentifiers(): array {
		if ($this->storage->getMode($this->key) === SelectionMode::INCLUDE) {
			return $this->storage->getStoredIdentifiers($this->key);
		} else {
			$excluded = $this->storage->getStoredIdentifiers($this->key);
			$all      = $this->storage->getStoredIdentifiers($this->getAllContext());
			return array_diff($all, $excluded);
		}
	}

	public function rememberAll(array $ids): static {
		$this->storage->add($this->getAllContext(), $ids);
		return $this;
	}

	public function setMode(SelectionMode $mode): void {
		$this->storage->setMode($this->key, $mode);
	}

	public function getMode(): SelectionMode {
		return $this->storage->getMode($this->key);
	}

	private function getAllContext(): string {
		return $this->key . '__ALL__';
	}

	public function destroy(): static {
		$this->storage->clear($this->key);
		$this->storage->clear($this->getAllContext());
		return $this;
	}
}