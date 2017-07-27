<?php
namespace Skeleton\Exceptions;


class ContextNotDefinedException extends SkeletonException
{
	public function __construct(string $name, string $key)
	{
		parent::__construct("There is no item for the key '$key' defined inside the context of $name'");
	}
}