<?php

namespace Tito10047\BatchSelectionBundle\Loader;

use Doctrine\Common\Collections\Collection;
use Tito10047\BatchSelectionBundle\Normalizer\IdentifierNormalizerInterface;

/**
 * Loader responsible for extracting identifiers from Doctrine Collection objects.
 */
class DoctrineCollectionLoader implements IdentityLoaderInterface
{
	private const DEFAULT_IDENTIFIER_PATH = 'id';

	public function __construct(
		private readonly IdentifierNormalizerInterface $resolver
	) { }


	/**
	 * @inheritDoc
	 */
	public function supports(mixed $source): bool
	{
		return $source instanceof Collection;
	}

	/**
	 * @inheritDoc
	 */
	public function getTotalCount(mixed $source): int
	{
		if (!$this->supports($source)) {
			throw new \InvalidArgumentException('Source must be a Doctrine Collection.');
		}

		/** @var Collection $source */
		return $source->count();
	}

	/**
	 * @param string $identifierPath *
	 *
	 * @inheritDoc
	 */
	public function loadAllIdentifiers(mixed $source, string $identifierPath = 'id'): array
	{
		if (!$this->supports($source)) {
			throw new \InvalidArgumentException('Source must be a Doctrine Collection.');
		}

		/** @var Collection $source */
		$identifiers = [];

		foreach ($source as $item) {
			$identifiers[] = $this->resolver->normalize($item, $identifierPath);
		}

		return $identifiers;
	}
}