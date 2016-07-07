<?php
namespace Skeleton;


use Skeleton\Exceptions;
use Skeleton\Base\IMap;
use Skeleton\Base\ConfigSearch;
use Skeleton\Base\IConfigLoader;
use Skeleton\Base\ISkeletonSource;
use Skeleton\Maps\SimpleMap;
use Skeleton\Tools\Knot\Knot;
use Skeleton\Loader\Loader;


class Skeleton implements ISkeletonSource
{
	/** @var IMap */
	private $map;
	
	/** @var IConfigLoader|null */
	private $configLoader;
	
	/** @var Loader */
	private $loader = null;
	
	
	public function __construct() 
	{
		$this->loader = new Loader();
		$this->setMap(new SimpleMap());
	}
	
	
	/**
	 * @param IMap $map
	 * @return static
	 */
	public function setMap(IMap $map) 
	{
		$this->map = $map;
		$this->map->setLoader($this->loader);
		return $this;
	}
	
	/**
	 * @return IMap
	 */
	public function getMap()
	{
		return $this->map;
	}
	
	/**
	 * @param IConfigLoader|null $loader
	 * @return static
	 */
	public function setConfigLoader(IConfigLoader $loader = null)
	{
		$this->configLoader = $loader;
		return $this;
	}
	
	/**
	 * @return IConfigLoader
	 */
	public function getConfigLoader()
	{
		return $this->configLoader;
	}
	
	/**
	 * @return static
	 */
	public function enableKnot()
	{
		$this->loader->setKnot(new Knot($this));
		return $this;
	}
	
	
	/**
	 * @param string $key
	 * @return object|string
	 */
	public function get($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if ($this->map->has($key)) 
			return $this->map->get($key);
		
		if (is_null($this->configLoader)) 
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		ConfigSearch::searchFor($key, $this->map, $this->configLoader);
		
		return $this->map->get($key);
	}
	
	/**
	 * @param string $key
	 * @param string|object $implementer
	 * @param int $flags
	 * @return static
	 */
	public function set($key, $implementer, $flags = Type::Instance)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		else if (!is_string($implementer) && !is_object($implementer))
			throw new Exceptions\InvalidImplementerException($implementer);
		
		$this->map->set($key, $implementer, $flags);
		
		return $this;
	}
}