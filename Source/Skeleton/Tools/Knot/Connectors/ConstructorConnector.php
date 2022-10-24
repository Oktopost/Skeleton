<?php
namespace Skeleton\Tools\Knot\Connectors;


use Skeleton\Tools\Knot\Base\AbstractObjectToSkeletonConnector;


class ConstructorConnector extends AbstractObjectToSkeletonConnector
{
	/**
	 * @param \ReflectionParameter $parameter
	 * @return mixed
	 */
	private function loadParameter(\ReflectionParameter $parameter)
	{
		$type = $parameter->getType();

		try
		{
			if (!($type instanceof \ReflectionNamedType))
			{
				throw new \ReflectionException();
			}

			$class = new \ReflectionClass($type->getName());
		}
		catch (\ReflectionException $e)
		{
			throw new \Exception(sprintf('Constructor parameter must be autoloaded but parameter type for parameter %s is missing or not supported', $parameter->getName()));
		}

		return $this->get($class->getName());
	}
	
	
	/**
	 * @param \ReflectionClass $class
	 * @return mixed
	 */
	public function connect(\ReflectionClass $class)
	{
		$values = [];
		$constructor = $class->getConstructor();
		
		if (is_null($constructor) || $constructor->getNumberOfRequiredParameters() == 0)
		{
			return $class->newInstance();
		}
		
		foreach ($constructor->getParameters() as $parameter)
		{
			$values[] = $this->loadParameter($parameter);
		}
		
		return $class->newInstanceArgs($values);
	}
}