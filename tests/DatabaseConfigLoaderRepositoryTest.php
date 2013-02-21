<?php

use Hailwood\DatabaseConfigLoader\DatabaseConfigLoader;
use Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderModel;
use Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderRepository;
use Mockery as m;

include_once( 'DatabaseConfigLoaderTestCase.php' );
include_once( __DIR__ . '/../vendor/autoload.php' );

class DatabaseConfigLoaderRepositoryTest extends DatabaseConfigLoaderTestCase {

    public $app;

    public function setUp(){
        $this->app = $this->createApplication();
        $this->resetTestData();
    }

	public function tearDown()
	{
		m::close();
	}


	public function testHasGroupIndicatesIfConfigGroupDoesNotExist()
	{
		$config = $this->getRepository();
		$this->assertFalse($config->hasGroup('package::key'));
	}

    public function testHasGroupIndicatesIfConfigGroupExists()
    {
        $config = $this->getRepository();
        $this->assertTrue($config->hasGroup('auth'));
        $this->assertTrue($config->hasGroup('beta::maps'));
    }


	public function testGetReturnsBasicItems()
	{
		$config = $this->getRepository();

		$this->assertEquals('User', $config->get('auth.model'));
		$this->assertEquals('Alpha Title', $config->get('alpha::title'));
		$this->assertEquals('New Zealand', $config->get('beta::maps.nz'));
	}


	public function testEntireArrayCanBeReturned()
	{
		$config = $this->getRepository();
		$this->assertEquals($this->getCharlie(), $config->get('charlie::config'));
	}


	public function testLoaderGetsCalledCorrectForNamespaces()
	{
		$config = $this->getRepository();

        $this->assertEquals('Alpha Title', $config->get('alpha::title'));
		$this->assertEquals('bar', $config->get('delta::group.arr.foo'));
	}

	/*public function testItemsCanBeSet()
	{
		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('production', 'foo', null)->andReturn(array('name' => 'dayle'));

		$config->set('foo.name', 'taylor');
		$this->assertEquals('taylor', $config->get('foo.name'));

		$config = $this->getRepository();
		$options = $this->getDummyOptions();
		$config->getLoader()->shouldReceive('load')->once()->with('production', 'foo', 'namespace')->andReturn(array('name' => 'dayle'));

		$config->set('namespace::foo.name', 'taylor');
		$this->assertEquals('taylor', $config->get('namespace::foo.name'));
	}


	public function testPackageRegistersNamespaceAndSetsUpAfterLoadCallback()
	{
		$config = $this->getMock('Illuminate\Config\Repository', array('addNamespace'), array(m::mock('Illuminate\Config\LoaderInterface'), 'production'));
		$config->expects($this->once())->method('addNamespace')->with($this->equalTo('rees'), $this->equalTo(__DIR__));
		$config->getLoader()->shouldReceive('cascadePackage')->once()->with('production', 'dayle/rees', 'group', array('foo'))->andReturn(array('bar'));
		$config->package('dayle/rees', __DIR__);
		$afterLoad = $config->getAfterLoadCallbacks();
		$results = call_user_func($afterLoad['rees'], $config, 'group', array('foo'));

		$this->assertEquals(array('bar'), $results);
	}*/


	protected function getRepository()
	{
        $repository = new DatabaseConfigLoaderRepository(new DatabaseConfigLoader( new DatabaseConfigLoaderModel() ), 'production');
        $repository->package('alpha/alpha', 'alpha/alpha');
        $repository->package('beta/beta', 'beta/beta');
        return $repository;
    }


	protected function getDummyOptions()
	{
		return array('foo' => 'bar', 'baz' => array('boom' => 'breeze'));
	}

}