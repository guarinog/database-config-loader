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
     *
     * @return mixed
     */
    public function get($key, $default = null){
        list( $namespace, $group, $item ) = $this->parseKey($key);

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
    public function set($key, $value){

        // We'll unset the key here, we need to do this because previous fetch
        // attempts before they key is actually created creates a slightly different
        // parsing value.
        unset( $this->parsed[$key] );

        // We'll manually set the parsed key here as setting a value requires a
        // slightly modified parsing. By default parseKey assumes that if a group
        // does not exist then we are meaning a key in the config group. But when
        // setting we need to be able to manually create a group on the fly so
        //
        // ::set('key', 'value')
        // If only a key is passed we assume we are dealing with a key from the config
        // group with no namespace
        //
        // ::set('namespace::key', 'value')
        // If only a namespace and a key are passed we assume we are dealing with a key
        // from the config group in the mentioned namespace
        //
        // Any time a . is present in the key we are going to assume the first section
        // (excluding the namespace) is the group.

        $explodedOnNamespace = explode('::', $key);

        if(count($explodedOnNamespace) > 1){
            $namespace = $explodedOnNamespace[0];
            $group     = $explodedOnNamespace[1];
        } else{
            $namespace = null;
            $group     = $key;
        }

        $explodedOnGroup = explode('.', $group);
        if(count($explodedOnGroup) > 1){
            $group = array_shift($explodedOnGroup);
            $item  = implode('.', $explodedOnGroup);
        } else{
            $group = 'config';
            $item  = $explodedOnGroup[0];
        }

        $this->setParsedKey($key, array($namespace, $group, $item));

        $collection = $this->getCollection($group, $namespace);
        // We'll wipe out the collection so that when we request it again it gets
        // reloaded from the database instead of using the cached version.
        unset( $this->items[$collection] );
        unset( $this->items[$namespace.'::config'] );

        $this->loader->set($value, $namespace, $group, $item, null);
    }

    /**
     * Register a package for cascading configuration.
     *
     * @param  string  $package
     * @param  string  $hint
     * @param  string  $namespace
     *
     * @return void
     */
    public function package($package, $hint, $namespace = null){
        $namespace = $this->getPackageNamespace($package, $namespace);

        $this->packages[] = $namespace;

        $this->addNamespace($namespace, $hint);
    }

}