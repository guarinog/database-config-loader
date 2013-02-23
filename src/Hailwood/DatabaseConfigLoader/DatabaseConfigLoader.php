<?php namespace Hailwood\DatabaseConfigLoader;

use Illuminate\Config\LoaderInterface;

class DatabaseConfigLoader implements LoaderInterface{

    /**
     * The eloquent instance.
     *
     * @var DatabaseConfigLoaderModel
     */
    protected $model;
    /**
     * All of the named path hints.
     *
     * @var array
     */
    protected $hints = array();
    /**
     * A cache of whether namespaces and groups exists.
     *
     * @var array
     */
    protected $exists = array();

    /**
     * Create a new database configuration loader.
     *
     * @param \Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderModel $model
     *
     * @return \Hailwood\DatabaseConfigLoader\DatabaseConfigLoader
     */
    public function __construct(DatabaseConfigLoaderModel $model){
        $this->model = $model;
    }

    /**
     * Load the given configuration group.
     *
     * @param  string  $environment
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function load($environment, $group, $namespace = null){
        $items = array();

        foreach(DatabaseConfigLoaderModel::fetchSettings($environment, $namespace, $group) as $item){
            switch(strtolower($item->type)){
            case 'string':
                $items[$item->key] = (string)$item->value;
                break;
            case 'integer':
                $items[$item->key] = (integer)$item->value;
                break;
            case 'double':
                $items[$item->key] = (double)$item->value;
                break;
            case 'boolean':
                $items[$item->key] = (boolean)$item->value;
                break;
            case 'array':
                $items[$item->key] = unserialize($item->value);
                break;
            case 'null':
                $items[$item->key] = null;
                break;
            default:
                $items[$item->key] = $item->value;
            }
        }

        return $items;
    }

    /**
     * Determine if the given group exists.
     *
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return bool
     */
    public function exists($group, $namespace = null){
        $key = $group . $namespace;

        // We'll first check to see if we have determined if this namespace and
        // group combination have been checked before. If they have, we will
        // just return the cached result so we don't have to hit the database.
        if(isset( $this->exists[$key] )){
            return $this->exists[$key];
        }

        // Finally, we can simply ask the database if the group exists.
        // We will also cache the value in an array so we don't have to go
        // through this process again on subsequent checks for the existing
        // of the config file.
        $exists = DatabaseConfigLoaderModel::exists($group, $namespace);

        return $this->exists[$key] = $exists;
    }

    /**
     * Because of the database component it's impossible to manually load the config, so this function is not needed
     * But, the interface demands it :(
     *
     * @param string $environment
     * @param string $package
     * @param string $group
     * @param array  $items
     *
     * @return array
     */
    public function cascadePackage($environment, $package, $group, $items){ }

    /**
     * Add a new namespace to the loader, But because we are doing a database query, there is no point,
     * But we leave it in here anyway because well, we can :D
     *
     * @param  string  $namespace
     * @param  string  $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint){
        $this->hints[$namespace] = $hint;
    }

    /**
     * Get the DatabaseConfigLoaderModel instance.
     *
     * @return DatabaseConfigLoaderModel
     */
    public function getModel(){
        return $this->model;
    }

    public function set($value, $package, $group, $item, $environment){
        unset($this->exists[$group.$package]);
        $type = null;

        $givenType = strtolower(gettype($value));

        switch($givenType){
        case 'string':
        case 'integer':
        case 'double':
        case 'boolean':
        case 'null':
            $type = $givenType;
            break;
        case 'array':
            $value = serialize($value);
            $type  = 'array';
            break;
        default:
            $type = null;
        }

        $this->model->set($value, $package, $group, $item, $environment, $type);
    }

}