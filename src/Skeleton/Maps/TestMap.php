<?php
namespace Skeleton\Maps;


use Skeleton\Type;
use Skeleton\Base\IMap;
use Skeleton\Base\ILoader;

use Skeleton\Exceptions;


class TestMap implements IMap
{
	/** @var IMap */
	private $originalMap;
	
	/** @var array */
	private $map;
	
	
	/**
	 * @param IMap $main
	 */
	public function __construct(IMap $main)
	{
		$this->originalMap = $main;
	}
	
	
	/**
	 * @return IMap
	 */
	public function getOriginal() 
	{
		return $this->originalMap;
	}
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		$this->originalMap->set($key, $value, $flags);
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (isset($this->map[$key]))
		{
			$value = $this->map[$key];
			
			if (is_string($value) && class_exists($value))
			{
				return new $value;
			}
			else
			{
				return $value;
			}
		}
		
		return $this->originalMap->get($key);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		return (isset($this->map[$key]) || $this->originalMap->has($key));
	}
	
	/**
	 * @param ILoader $loader
	 */
	public function setLoader(ILoader $loader)
	{
		$this->originalMap->setLoader($loader);
	}
	
	
	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function override($key, $value) 
	{
		$this->map[$key] = $value;
	}
	
	public function clear()
	{
		$this->map = [];
	}
}