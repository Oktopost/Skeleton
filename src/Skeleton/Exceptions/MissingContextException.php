<?php
namespace Skeleton\Exceptions;


class MissingContextException extends SkeletonException
{
	public function __construct(string $target)
	{
		parent::__construct("A contact is required for the class '$target'", 102);
	}
}