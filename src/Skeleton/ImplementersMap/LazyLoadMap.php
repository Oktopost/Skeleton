<?php
namespace Skeleton\ImplementersMap;


use \Skeleton\Type;
use \Skeleton\ISingleton;
use \Skeleton\Base\IMap;

use \Skeleton\Exceptions;


class LazyLoadMap implements IMap 
{
	/** @var array */
	private $m_aValues = [];
	
	/** @var array */
	private $m_aMap = [];
	
	
	/**
	 * @param string|array|callable $value
	 * @return object|
	 */
	private function getInstance($value) 
	{
		if (is_string($value))
			return new $value;
		
		if (is_callable($value))
			return $value();
		
		return $value;
	}
	
	/**
	 * @param string $key
	 * @param string $value
	 * @param string $type
	 * @return object
	 */
	private function getObject($key, $value, $type)
	{
		$instance = $this->getInstance($value);
		
		if (is_object($instance) && ($instance instanceof ISingleton || $type == Type::Singleton))
		{
			$this->m_aMap[$key] = $instance;
		}
		
		return $instance;
	}
	
	
	/**
	 * @param string $key
	 * @param string|object $value
	 * @param int $flags
	 */
	public function set($key, $value, $flags = Type::Instance)
	{
		if ($this->has($key))
			throw new Exceptions\ImplementerAlreadyDefinedException($key);
		
		if ((is_string($value) && $flags != Type::StaticClass) || 
			is_callable($value)) 
		{
			$this->m_aMap[$key] = [$value, $flags];
		} 
		else 
		{
			$this->m_aValues[$key] = $value;
		}
	}
	
	/**
	 * @param string $key
	 * @return string|object
	 */
	public function get($key) 
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		if (isset($this->m_aValues[$key]))
			return $this->m_aValues[$key];
		
		if (!isset($this->m_aMap[$key]))
			throw new Exceptions\ImplementerNotDefinedException($key);
		
		$data = $this->m_aMap[$key];
		
		if (!is_array($data)) 
			return $data;
		
		return $this->getObject($key, $data[0], $data[1]);
	}
	
	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if (!is_string($key))
			throw new Exceptions\InvalidKeyException($key);
		
		return isset($this->m_aValues[$key]) || isset($this->m_aMap[$key]);
	}
}