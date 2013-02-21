<?php
use Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderModel as DBConfig;

require( __DIR__ . '/../../../../bootstrap/autoload.php' );

class DatabaseConfigLoaderTestCase extends Illuminate\Foundation\Testing\TestCase{

    protected $app;

    /**
     * Creates the application.
     *
     * @return Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication(){
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require  __DIR__ . '/../../../../bootstrap/start.php';
    }

    public function resetTestData(){

        DB::statement('truncate table dbconfig_settings');

        DBConfig::Create(array('group' => 'auth', 'key' => 'model', 'value' => 'User', 'type' => 'string'));

        DBConfig::Create(array('group'       => 'auth', 'key' => 'model', 'value' => 'Dev User', 'type' => 'string',
                               'environment' => 'development'));

        DBConfig::Create(array('package' => 'alpha', 'group' => 'config', 'key' => 'title', 'value' => 'Alpha Title',
                               'type'    => 'string'));

        DBConfig::Create(array('package' => 'beta', 'group' => 'maps', 'key' => 'nz', 'value' => 'New Zealand',
                               'type'    => 'string'));

        DBConfig::Create(array('package' => 'beta', 'group' => 'maps', 'key' => 'nz', 'value' => 'New Z-dev',
                               'type'    => 'string', 'environment' => 'development'));

        DBConfig::Create(array('package' => 'charlie', 'group' => 'config', 'key' => 'first',
                               'value'   => 'Charlie is First!', 'type' => 'string'));

        DBConfig::Create(array('package' => 'charlie', 'group' => 'config', 'key' => 'finger',
                               'value'   => 'Charlie bit my finger!', 'type' => 'string'));

        DBConfig::Create(array('package' => 'charlie', 'group' => 'config', 'key' => 'last',
                               'value'   => 'Charlie is Last!', 'type' => 'string'));

        DBConfig::Create(array('package' => 'delta', 'group' => 'group', 'key' => 'arr',
                               'value'   => serialize(array('foo' => 'bar')), 'type' => 'array'));
    }

    public function getCharlie(){
        return array(
            'first'  => 'Charlie is First!',
            'finger' => 'Charlie bit my finger!',
            'last'   => 'Charlie is Last!'
        );
    }
}
