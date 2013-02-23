<?php

use Hailwood\DatabaseConfigLoader\DatabaseConfigLoader;
use Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderModel;
use Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderRepository;
use Mockery as m;

include_once( 'DatabaseConfigLoaderTestCase.php' );
include_once( __DIR__ . '/../vendor/autoload.php' );

class DatabaseConfigLoaderRepositoryTest extends DatabaseConfigLoaderTestCase{

    public $app;

    public function setUp(){
        $this->app = $this->createApplication();
        $this->resetTestData();
    }

    public function tearDown(){
        m::close();
    }

    public function testHasGroupIndicatesIfConfigGroupDoesNotExist(){
        $config = $this->getRepository();
        $this->assertFalse($config->hasGroup('package::key'));
    }

    public function testHasGroupIndicatesIfConfigGroupExists(){
        $config = $this->getRepository();
        $this->assertTrue($config->hasGroup('auth'));
        $this->assertTrue($config->hasGroup('beta::maps'));
    }

    public function testGetReturnsBasicItems(){
        $config = $this->getRepository();

        $this->assertEquals('User', $config->get('auth.model'));
        $this->assertEquals('Alpha Title', $config->get('alpha::title'));
        $this->assertEquals('New Zealand', $config->get('beta::maps.nz'));
    }

    public function testEntireArrayCanBeReturned(){
        $config = $this->getRepository();
        $this->assertEquals($this->getCharlie(), $config->get('charlie::config'));
    }

    public function testLoaderGetsCalledCorrectForNamespaces(){
        $config = $this->getRepository();

        $this->assertEquals('Alpha Title', $config->get('alpha::title'));
        $this->assertEquals('bar', $config->get('delta::group.arr.foo'));
    }

    protected function getRepository(){
        $repository = new DatabaseConfigLoaderRepository( new DatabaseConfigLoader( new DatabaseConfigLoaderModel() ), 'production' );
        $repository->package('alpha/alpha', 'alpha/alpha');
        $repository->package('beta/beta', 'beta/beta');
        $repository->package('charlie/charlie', 'charlie/charlie');
        $repository->package('delta/delta', 'delta/delta');
        $repository->package('echo/echo', 'echo/echo');
        return $repository;
    }

    public function testItemsCanBeSet(){
        $config = $this->getRepository();

        //simple set
        $config->set('foo.name', 'taylor');
        $this->assertEquals('taylor', $config->get('foo.name'));

        //simple update
        $config->set('foo.name', 'matthew');
        $this->assertEquals('matthew', $config->get('foo.name'));

        //namespace set
        $config->set('echo::foo.name', 'taylor');
        $this->assertEquals('taylor', $config->get('echo::foo.name'));

        //namespace update
        $config->set('echo::foo.name', 'matthew');
        $this->assertEquals('matthew', $config->get('echo::foo.name'));
    }

    public function testTypeSerializationWorks(){
        $config = $this->getRepository();
        $array = array(
            'a_c' => array('a', 'b', 'c'),
            'd_i' => array('d', 'e', 'f' => array('g', 'h', 'i')),
        );

        $config->set('foo.array', $array);
        $this->assertSame($array, $config->get('foo.array'));

        $config->set('foo.bfalse', false);
        $this->assertSame(false, $config->get('foo.bfalse'));

        $config->set('foo.btrue', true);
        $this->assertSame(true, $config->get('foo.btrue'));

        $config->set('foo.integer500', 500);
        $this->assertSame(500, $config->get('foo.integer500'));

        $config->set('foo.integer1', 1);
        $this->assertSame(1, $config->get('foo.integer1'));
    }

    public function testSubsetOfArrayCanBeSet(){
        $config = $this->getRepository();

        $this->assertEmpty($config->get('alpha::group.key.s1.s2'));
        $expected = array('s1' => array('s2' => 'k2val'));
        $config->set('alpha::group.key.s1.s2', 'k2val');
        $this->assertEquals('k2val', $config->get('alpha::group.key.s1.s2'));

        $this->assertEquals($expected, $config->get('alpha::group.key'));

    }

}