<?php

use Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderModel as DBConfig;
use Illuminate\Support\Facades\DB;
use Mockery as m;

include_once( 'DatabaseConfigLoaderTestCase.php' );
include_once( __DIR__ . '/../vendor/autoload.php' );

class DatabaseConfigLoaderTest extends DatabaseConfigLoaderTestCase{

    public $app;

    public function setUp(){
        $this->app = $this->createApplication();
        $this->resetTestData();
    }

    public function tearDown(){
        m::close();
    }

    /**
     * Tests if an empty array is returned when we request data that does no exist
     */
    public function testEmptyArrayIsReturnedOnInvalidRequest(){
        $loader = $this->getLoader();
        $this->assertEquals(array(), $loader->load('environment', 'group', 'namespace'));
    }

    /**
     * Tests if a basic array is returned when we request a group with an environment where no overide exists
     */
    public function testBasicArrayIsReturnedWithDefaultEnvironment(){
        $loader = $this->getLoader();
        $array  = $loader->load('local', 'auth', null);

        $this->assertEquals(array('model' => 'User'), $array);
    }

    /**
     * Tests if a basic array is returned when we request a group that has an environment override
     * So also doubles to ensure the environment overrides are applied
     */
    public function testBasicArrayIsReturnedWithOverrideEnvironment(){
        $loader = $this->getLoader();
        $array  = $loader->load('development', 'auth', null);

        $this->assertEquals(array('model' => 'Dev User'), $array);
    }

    public function testGroupExistsReturnsTrueWhenTheGroupExists()
    {
        $loader = $this->getLoader();
        //$loader->getModel()->shouldReceive('exists')->once()->with(__DIR__.'/app.php')->andReturn(true);
        $this->assertTrue($loader->exists('auth'));
    }

    public function testGroupExistsReturnsTrueWhenNamespaceGroupExists()
    {
        $loader = $this->getLoader();
        $this->assertTrue($loader->exists('maps', 'beta'));
    }

    public function testGroupExistsReturnsFalseWhenNamespaceGroupDoesntExists()
    {
        $loader = $this->getLoader();
        $this->assertFalse($loader->exists('nonexisto', 'beta'));
    }

    protected function getLoader(){
        //m::mock('Illuminate\Filesystem\Filesystem')
        return new Hailwood\DatabaseConfigLoader\DatabaseConfigLoader( new Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderModel() );
    }

}