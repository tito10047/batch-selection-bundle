<?php

namespace Tito10047\BatchSelectionBundle\Loader;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Tito10047\BatchSelectionBundle\Service\IdentityResolverInterface;

/**
 * Loader responsible for handling Pagerfanta Adapters.
 * It uses delegation and reflection to extract the underlying data source (e.g., QueryBuilder)
 * and perform an optimized single-query load operation.
 */
class PagerfantaAdapterLoader implements IdentityLoaderInterface
{
	public function __construct(
		// Inject the specialized loader we want to delegate to for optimization
		private readonly DoctrineQueryBuilderLoader $doctrineLoader
	) {}

	/**
	 * @inheritDoc
	 */
	public function supports(mixed $source): bool
	{
		return $source instanceof AdapterInterface;
	}

	/**
	 * @inheritDoc
	 * Total count is retrieved directly from the adapter, which is always fast.
	 * @param AdapterInterface $source
	 */
	public function getTotalCount(mixed $source): int
	{
		if (!$this->supports($source)) {
			throw new \InvalidArgumentException('Source must be an instance of Pagerfanta AdapterInterface.');
		}

		/** @var AdapterInterface $source */
		return $source->getNbResults();
	}

	/**
	 * @inheritDoc
	 *
	 * Delegates to the optimized Doctrine loader if the source is an ORM adapter.
	 * Fallback for other sources (e.g., ArrayAdapter) must use the adapter's own limited mechanism
	 * or rely on the fact that ArrayAdapter sources are typically small enough for memory.
	 *
	 * @param AdapterInterface $source
	 * @return array<int|string>
	 */
	public function loadAllIdentifiers(mixed $source, string $identifierPath = 'id'): array
	{
		if (!$this->supports($source)) {
			throw new \InvalidArgumentException('Source must be an instance of Pagerfanta AdapterInterface.');
		}

		/** @var AdapterInterface $source */

		// 1. Optimization Check: Is the underlying source Doctrine ORM?
		if ($source instanceof QueryAdapter) {
			// Extract the underlying QueryBuilder safely via reflection
			$qb = $this->extractQueryBuilderFromAdapter($source);

			// Delegate the optimized single-query operation to the dedicated loader
			return $this->doctrineLoader->loadAllIdentifiers($qb, $identifierPath);
		}

		// 2. Fallback: For non-optimizable sources (e.g., custom adapters, ArrayAdapters with large arrays).
		// Since AdapterInterface only guarantees fetching data in chunks (slice()),
		// retrieving all identifiers efficiently without knowing the source is impossible.
		// We throw an exception for safety, forcing the user to implement a specialized loader for large custom sources.
		throw new \RuntimeException(sprintf(
			'Cannot efficiently load all identifiers from Pagerfanta Adapter "%s". Implement a dedicated loader.',
			get_debug_type($source)
		));
	}

	/**
	 * Uses Reflection to safely access the private QueryBuilder property of the Doctrine ORM Adapter.
	 * This method is an necessary architectural breach for achieving high performance optimization.
	 */
	private function extractQueryBuilderFromAdapter(QueryAdapter $adapter): QueryBuilder
	{
		try {
			$reflection = new \ReflectionClass($adapter);
			$property = $reflection->getProperty('queryBuilder');
			$property->setAccessible(true);

			$qb = $property->getValue($adapter);

			if (!$qb instanceof QueryBuilder) {
				throw new \RuntimeException('Extracted property is not a QueryBuilder.');
			}
			return $qb;
		} catch (\Throwable $e) {
			throw new \RuntimeException('Failed to extract QueryBuilder from Pagerfanta Adapter.', 0, $e);
		}
	}
}