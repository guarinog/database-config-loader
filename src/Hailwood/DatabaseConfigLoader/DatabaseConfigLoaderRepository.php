<?php namespace Hailwood\DatabaseConfigLoader;

use Illuminate\Config\Repository;

/**
 * Class DatabaseConfigLoaderRepository
 * @package Hailwood\DatabaseConfigLoader
 *
 * @property DatabaseConfigLoader $loader
 */
class DatabaseConfigLoaderRepository extends Repository{

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        // Configuration items are actually keyed by "collection", which is simply a
        // combination of each namespace and groups, which allows a unique way to
        // identify the arrays of configuration items for the particular files.
        $collection = $this->getCollection($group, $namespace);

        $this->load($group, $namespace, $collection);

        return array_get($this->items[$collection], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  string      $key
     * @param  mixed       $value
     * @param  null|string $environment
     *
     * @return void
     */
    public function set($key, $value, $environment = null)
    {
        unset($this->parsed[$key]);
        d('WORKING ON '.$key);
        list($namespace, $group, $item) = $this->parseKey($key);

        $collection = $this->getCollection($group, $namespace);

        dd($key, $value, $environment, $namespace, $group, $item, $collection);

        // We'll wipe out the collection so that when we request it again it gets
        // reloaded from the database instead of using the cached version.
        unset($this->items[$collection]);

        $this->loader->set($value, $namespace, $group, $item, $environment);
    }

    /**
     * Register a package for cascading configuration.
     *
     * @param  string  $package
     * @param  string  $hint
     * @param  string  $namespace
     * @return void
     */
    public function package($package, $hint, $namespace = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);

        $this->packages[] = $namespace;

        $this->addNamespace($namespace, $hint);
    }

}