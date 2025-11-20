<?php

namespace Tito10047\BatchSelectionBundle\Loader;


use Tito10047\BatchSelectionBundle\Normalizer\IdentifierNormalizerInterface;

class ArrayLoader implements IdentityLoaderInterface
{
	public function __construct(
		private readonly IdentifierNormalizerInterface $resolver
	) { }

	public function supports(mixed $source): bool
	{
		return is_array($source);
	}

	public function loadAllIdentifiers(mixed $source, ?string $identifierPath): array
	{
		if (!is_array($source)) {
			throw new \InvalidArgumentException('Source must be an array.');
		}

		$identifiers = [];

		foreach ($source as $item) {
			$identifiers[] = $this->resolver->normalize($item, $identifierPath);
		}

		return $identifiers;
	}


	public function getTotalCount(mixed $source): int {
		return count($source);
	}
}