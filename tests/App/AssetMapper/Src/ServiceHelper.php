<?php

namespace Tito10047\BatchSelectionBundle\Tests\App\AssetMapper\Src;

use Tito10047\BatchSelectionBundle\Normalizer\IdentifierNormalizerInterface;
use Twig\Loader\LoaderInterface;

class ServiceHelper {

	/**
	 * @param IdentifierNormalizerInterface[] $normalizers
	 * @param LoaderInterface[] $loaders
	 */
	public function __construct(
		public readonly iterable $normalizers,
		public readonly iterable $loaders
	) { }
}