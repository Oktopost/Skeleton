<?php
namespace Skeleton;


use Traitor\TSingleton;
use Skeleton\Exceptions\SkeletonException;


trait TModule
{
	use TSingleton;
	
	
	/** @var static[] */
	private $subModules;
	
	/** @var array */
	private $components;
	
	/** @var Skeleton */
	private $skeleton;
	
	
	/**
	 * @return array
	 */
	protected function getSubModules() { return []; }
	
	
	/**
	 * @param static $instance
	 */
	protected static function initialize($instance) 
	{
		$instance->subModules	= $instance->getSubModules();
		$instance->components	= $instance->getComponent();
		$instance->skeleton		= $instance->skeleton();
	}
	
	
	/**
	 * @return Skeleton
	 */
	protected abstract function skeleton();
	
	/**
	 * @return array
	 */
	protected abstract function getComponent();
	
	
	/**
	 * @param string $name
	 * @param mixed $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		if (isset(self::$instance->components[$name])) 
			return self::$instance->skeleton->get(self::$instance->components[$name]);
		
		if (isset(self::$instance->subModules[$name]))
		{
			$subModule = self::$instance->subModules[$name];
			return $subModule::instance();
		}
		
		throw new SkeletonException("Unrecognized component or sub module '$name'");
	}
	
	/**
	 * @param string $name
	 * @param mixed $arguments
	 * @return mixed
	 */
	public static function __callStatic($name, $arguments)
	{
		if (isset(self::instance()->components[$name])) 
			return self::$instance->skeleton()->get(self::$instance->components[$name]);
		
		if (isset(self::$instance->subModules[$name]))
		{
			$subModule = self::$instance->subModules[$name];
			return $subModule::instance();
		}
		
		throw new SkeletonException("Unrecognized component or sub module '$name'");
	}
}