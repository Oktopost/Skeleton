<?php
namespace Skeleton\Base;


class ConfigSearchTest extends \SkeletonTestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMap
	 */
	private function mockMap() 
	{
		return $this->getMock(IMap::class);
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IConfigLoader
	 */
	private function mockLoader() 
	{
		return $this->getMock(IConfigLoader::class);
	}
	
	
	public function test_get_ConfigLoaderCalledWithCorrectValues()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$loader->expects($this->once())->method('tryLoad')->with('a');
		
		ConfigSearch::searchFor('a\b', $map, $loader);
	}
	
	public function test_get_ComplexKey_LoaderCalledForEachPart()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$expected = [
			'some/complex/namespace',
			'some/complex',
			'some',
		];
		
		$loader->expects($this->exactly(3))
			->method('tryLoad')
			->willReturnCallback(function ($path) use (&$expected) {
				$this->assertSame(array_shift($expected), $path);
				return false;
			});
		
		ConfigSearch::searchFor('some\complex\namespace\cls', $map, $loader);
	}
	
	public function test_get_ConfigFound_StopLoadingConfigs()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$map->expects($this->exactly(1))->method('has')->willReturn(true);
		
		$expected = [
			['some/complex/namespace', false],
			['some/complex', true],
		];
		
		$loader->expects($this->exactly(2))
			->method('tryLoad')
			->willReturnCallback(function ($path) use (&$expected) {
				[$expectedPath, $return] = array_shift($expected);
				$this->assertSame($expectedPath, $path);
				return $return;
			});
		
		ConfigSearch::searchFor('some\complex\namespace\cls', $map, $loader);
	}
	
	public function test_get_CalledForClassNotInNamespace_LoaderCalledForGlobal()
	{
		$map = $this->mockMap();
		$loader = $this->mockLoader();
		
		$loader->expects($this->exactly(1))->method('tryLoad')->with('Global');
		
		ConfigSearch::searchFor('cls', $map, $loader);
	}
}
