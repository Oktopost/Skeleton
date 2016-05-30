<?php
namespace Skeleton;


use Skeleton\ImplementersMap\SimpleMap;


class some_module  
{
	use TModule;
	
	protected function skeleton() { return null; }
	protected function getComponent() { return []; }
}

/**
 * @method static string a()
 * @method static string bb()
 * @method static string mod_a()
 */
class TModuleTestHelper 
{
	use TModule;
	
	/** @var Skeleton */
	private $skeleton = null;
	
	
	/**
	 * @return Skeleton
	 */
	protected function skeleton()
	{
		if (!$this->skeleton)
		{
			$map = new SimpleMap();
			
			$map->set('a', 'a_val');
			$map->set('b', 'b_val');
			
			$this->skeleton = new Skeleton();
			$this->skeleton->setMap($map);
		}
		
		return $this->skeleton;
	}
	
	protected function getSubModules()
	{
		return [
			'mod_a'	=> some_module::class
		];
	}
	
	/**
	 * @return array
	 */
	protected function getComponent()
	{
		return [
			'a'		=> 'a',
			'bb'	=> 'b'
		];
	}
}



class TModuleTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Skeleton\Exceptions\SkeletonException
	 */
	public function test_get_NotFound_ErrorThrown()
	{
		TModuleTestHelper::aa();
	}
	
	/**
	 * @expectedException \Skeleton\Exceptions\SkeletonException
	 */
	public function test_instance_get_NotFound_ErrorThrown()
	{
		TModuleTestHelper::instance()->aa();
	}
	
	
	public function test_get_ElementFound_ElementReturned()
	{
		$this->assertEquals('b_val', TModuleTestHelper::bb());
	}
	
	public function test_get_instance_ElementFound_ElementReturned()
	{
		$this->assertEquals('b_val', TModuleTestHelper::instance()->bb());
	}
	
	public function test_get_SubModuleFound_ElementReturned()
	{
		$this->assertInstanceOf(some_module::class, TModuleTestHelper::mod_a());
	}
	
	public function test_get_instance_SubModuleFound_ElementReturned()
	{
		$this->assertInstanceOf(some_module::class, TModuleTestHelper::instance()->mod_a());
	}
}