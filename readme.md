# Database Config Loader : Database powered configuration for Laravel

`Database Config Loader` is a composer package for the [Laravel PHP Framework](http://laravel.com/) that works in tandem with the default config package to allow developers to easily add user-configurable settings to their app and packages.

### Key Features

* Utilizes the same syntax as the Laravel config package (No new syntax to learn)
* Unlike calls to `Config::set()` calls to `DBconfig::set()` are persistant!
* Backed by Eloquent for the database component means that it works anywhere Laravel does!
* Automatic type serialization means configuration items can be s `string`, `integer`, `boolean` or even `array` and the type you save is the type you get back!
* Supports nested keys just like the Laravel Config package
* Support for environment based variables
* + More...

### Quick Start

1. Add the package to composer.json

        "hailwood/database-config-loader": "*",

2. Add the service provider to your application's `app.php` under the `providers` key

        'Hailwood\DatabaseConfigLoader\DatabaseConfigLoaderServiceProvider'

4. Add the facade to your application's `app.php` under the `aliases` key

        'DBconfig' => 'Hailwood\DatabaseConfigLoader\Facades\DatabaseConfigLoader'

# Methods

### ::set()
<small>Set also works to update a configuration item</small>

The set method is used to set configuration options and follows the same standard as the Laravel Config package which means it supports namespacing, groups (aka files), and nested keys.

All `DBconfig::set(...)` methods support an optional third parameter `$environment` which is the environment the variable key should be used in, these will automatically cascade just like the standard Config package!

#### #1.0: Global configuration
<small>This method is actually just a shortcut to `DBconfig::set('config.key', 'value')`!</small>

        DBconfig::set('key', 'value')

Equivalent to having the following in `app/config/config.php`

        'key' => 'value'

#### #1.1: Global configuration with group
        DBconfig::set('group.key', 'value')

Equivalent to having the following in `app/config/group.php`

        'key' => 'value'

#### #1.2: Global configuration with group and environment
        DBconfig::set('group.key', 'value', 'local')

Equivalent to having the following in `app/config/local/group.php`

        'key' => 'value'

#### #2.0: Package configuration
<small>This method is actually just a shortcut to `DBconfig::set('package::config.key', 'value')`!</small>

        DBconfig::set('package::key', 'value')

Equivalent to having the following in `app/config/packages/vendor/package/config.php`

        'key' => 'value'

#### #2.1: Package configuration with group
<small>Packages need to be [registered with DBconfig](#registeringPackages), Actually, they do with the standard Config package too, but Laravel handles this for you when you call `$this->package(...)` from your service provider!</small>

        DBconfig::set('package::group.key', 'value')

Equivalent to having the following in `app/config/packages/vendor/package/group.php`

        'key' => 'value'

#### #2.2: Package configuration with group and environment
        DBconfig::set('package::group.key', 'value', 'local')

Equivalent to having the following in `app/config/packages/vendor/package/local/group.php`

        'key' => 'value'

#### #3.0 Nested configuration keys
<small>Nested configuration keys are supported when using any of the *with group* methods. and has [a bit of magic](#30-nested-configuration-keys---a bit of magic)

        DBconfig::set('config.key.subKey1.subKey2.subKey3', 'value')

Equivalent to having the following in `app/config/config.php`

        'key' => array(
            'subKey1' => array(
                'subKey2' => array(
                    'subKey3' => 'value'
                )
            )
        )

And running:

        DBconfig::set('config.key.subKey1.subKey2.subKey4', 'value2')

After the first line would make the equivalent be

        'key' => array(
            'subKey1' => array(
                'subKey2' => array(
                    'subKey3' => 'value',
                    'subKey4' => 'value2'
                )
            )
        )

#### #3.0 Nested configuration keys - a bit of magic!
Notice the array above, let's create the initial key shall we?

        DBconfig::set('config.key', array(
            'subKey1' => array(
                'subKey2' => array(
                    'subKey3' => 'value'
                )
            )
        ));

We need to do that because we cannot set a value on something that doesn't exist, so we need to create the path right? **wrong!** this is a Laravel package, and that is anything but elegant!

So, `Database Config Loader` does a bit of magic for you, Just call `DBconfig::set('config.key.subKey1.subKey2.subKey3', 'value')` and we'll do the rest! 

### ::set()
The get method is used to retrieve configuration options and follows the same standard as the Laravel Config package, which means it supports namespacing, groups (aka files), and nested keys.

The get method is the same as the `set` method, except in only takes on parameter, the key, so we won't detail it all here again, instead just view `set` above! 

::has(), ::hasGroup(),...
`Database config loader` supports all method used by the standard Laravel Config package, so view the official Laravel documentation for other methods!