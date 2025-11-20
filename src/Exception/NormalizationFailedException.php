<?php

namespace Tito10047\BatchSelectionBundle\Exception;

/**
 * Thrown when the IdentifierResolver cannot find any suitable normalizer
 * to convert a complex item (object, non-scalar) into a scalar identifier.
 */
class NormalizationFailedException extends \RuntimeException
{
	// Simple custom exception class
}