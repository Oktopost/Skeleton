<?php
namespace Skeleton\Tools\Knot\Connectors;


use Skeleton\Base\ISkeletonSource;
use Skeleton\Tools\Annotation\Extractor;


class PropertyConnectorTest extends \PHPUnit_Framework_TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|ISkeletonSource */
	private $skeleton;
	
	
	/**
	 * @return PropertyConnector
	 */
	private function getPropertyConnector()
	{
		$this->skeleton = $this->getMock(ISkeletonSource::class);
		return (new PropertyConnector())
			->setSkeleton($this->skeleton)
			->setExtractor(new Extractor());
	}
	
	private function expectSkeletonNotCalled()
	{
		$this->skeleton->expects($this->never())->method('get');
	}
	
	/**
	 * @param string $type
	 * @param mixed $return
	 */
	private function expectSkeletonCalledFor($type, $return = null)
	{
		$this->skeleton->expects($this->once())->method('get')->with($type)->willReturn($return);
	}
	
	/**
	 * @param mixed $value
	 */
	private function setSkeletonToReturn($value)
	{
		$this->skeleton->method('get')->willReturn($value);
	}
	
	/**
	 * @param PropertyConnector $connector
	 * @param string $type
	 * @return mixed
	 */
	private function invokeConnect(PropertyConnector $connector, $type)
	{
		$instance = new $type;
		$connector->connect(new \ReflectionClass($type), $instance);
		return $instance;
	}
	
	
	public function test_setSkeleton_ReturnSelf()
	{
		/** @var ISkeletonSource $skeleton */
		$skeleton = $this->getMock(ISkeletonSource::class);
		$obj = new PropertyConnector();
		
		$this->assertSame($obj, $obj->setSkeleton($skeleton));
	}
	
	
	public function test_setExtractor_ReturnSelf()
	{
		$obj = new PropertyConnector();		
		$this->assertSame($obj, $obj->setExtractor(new Extractor()));
	}
	
	
	public function test_connect_EmptyClass_SkeletonNotCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_PropertyConnector_Helper_EmptyClass::class);
	}
	
	public function test_connect_NoAutoloadParams_SkeletonNotCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonNotCalled();
		$this->invokeConnect($obj, test_PropertyConnector_Helper_NoAutoload::class);
	}
	
	public function test_connect_PublicAutoloadParameter_SkeletonCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonCalledFor('Skeleton\Tools\Knot\Connectors\PubType', 1);
		
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_PublicAutoload::class);
		$this->assertEquals(1, $instance->pub);
	}
	
	public function test_connect_ProtectedAutoloadParameter_SkeletonCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonCalledFor('Skeleton\Tools\Knot\Connectors\ProtType', 2);
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_ProtectedAutoload::class);
		$this->assertEquals(2, $instance->get());
	}
	
	public function test_connect_PrivTypeAutoloadParameter_SkeletonCalled()
	{
		$obj = $this->getPropertyConnector();
		$this->expectSkeletonCalledFor('Skeleton\Tools\Knot\Connectors\PrivType', 3);
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_PrivateAutoload::class);
		$this->assertEquals(3, $instance->get());
	}
	
	public function test_connect_NumberOfProperties_AllLoaded()
	{
		$obj = $this->getPropertyConnector();
		$this->setSkeletonToReturn('value');
		$instance = $this->invokeConnect($obj, test_PropertyConnector_Helper_NumberOfProperties::class);
		
		$this->assertEquals('value', $instance->pub);
		$this->assertEquals('value', $instance->get());
	}
	
	/**
	 * @expectedException \Exception
	 */
	public function test_connect_PropertyHasNoType_ErrorThrown()
	{
		$obj = $this->getPropertyConnector();
		$this->invokeConnect($obj, test_PropertyConnector_Helper_NoType::class);
	}
	
	
	public function test_connect_PropertyHasRelativeNamespacePath_PathFixed()
	{
		$obj = $this->getPropertyConnector();
		
		$this->expectSkeletonCalledFor('Skeleton\Tools\Knot\Connectors\Name');
		
		$this->invokeConnect($obj, test_PropertyConnector_TestRelativeNamespace::class);
	}
	
	public function test_connect_PropertyHasFullNamespacePath_ProvidedPathIsUsed()
	{
		$obj = $this->getPropertyConnector();
		
		$this->expectSkeletonCalledFor('Full\Knot\Name');
		
		$this->invokeConnect($obj, test_PropertyConnector_TestFullNamespace::class);
	}
}


class test_PropertyConnector_Helper_EmptyClass {}

class test_PropertyConnector_Helper_NoAutoload 
{
	private		$a;
	protected	$b;
	public		$c;
}

class test_PropertyConnector_Helper_PublicAutoload
{
	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @autoload
	 * @var PubType
	 */
	public $pub;
}

class test_PropertyConnector_Helper_ProtectedAutoload
{
	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @autoload
	 * @var ProtType
	 */
	protected $prot;
	
	public function get() { return $this->prot; }
}

class test_PropertyConnector_Helper_PrivateAutoload
{
	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @autoload
	 * @var PrivType
	 */
	private	$priv;
	
	public function get() { return $this->priv; }
}

class test_PropertyConnector_Helper_NumberOfProperties
{
	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @autoload
	 * @var PrivType
	 */
	private $priv;
	
	/** @noinspection PhpUndefinedClassInspection */
	/**
	 * @autoload
	 * @var PubType
	 */
	public $pub;
	
	public function get() { return $this->priv; }
}

class test_PropertyConnector_Helper_NoType
{
	/**
	 * @autoload
	 */
	private $noType;
}

class test_PropertyConnector_TestRelativeNamespace
{
	/** @noinspection PhpUndefinedClassInspection */
	/** @noinspection PhpUndefinedNamespaceInspection */
	/**
	 * @autoload
	 * @var Knot\Name
	 */
	private $noType;
}

class test_PropertyConnector_TestFullNamespace
{
	/** @noinspection PhpUndefinedClassInspection */
	/** @noinspection PhpUndefinedNamespaceInspection */
	/**
	 * @autoload
	 * @var \Full\Knot\Name
	 */
	private $noType;
}