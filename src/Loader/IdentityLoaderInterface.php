<?php

namespace Tito10047\BatchSelectionBundle\Loader;

interface IdentityLoaderInterface {

	public function loadAllIdentifiers(mixed $source, string $identifierPath = 'id'): array;


	public function getTotalCount(mixed $source): int;

	public function supports(mixed $source):bool;
}