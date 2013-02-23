<?php namespace Hailwood\DatabaseConfigLoader;

use Illuminate\Database\Eloquent;
use Illuminate\Support\Facades\DB;

class DatabaseConfigLoaderModel extends Eloquent\Model{

    public $timestamps = false;
    protected $table = 'dbconfig_settings';

    public static function exists($group, $package = null){
        return ! self::fetchSettings(null, $package, $group)->isEmpty();
    }

    public static function fetchSettings($environment, $package, $group){

        $model = self::WhereIn('id', function ($q) use ($environment){
            $q->select(DB::raw('COALESCE(MIN(CASE WHEN environment = "' . $environment . '" THEN id END), MIN(id))'))
                ->from(( new self )->getTable())
                ->groupBy('package', 'key');
        });

        is_null($package) ? $model->whereNull('package') : $model->where('package', $package);

        return $model->where('group', $group)->get();
    }

    public static function set($value, $package, $group, $key, $environment, $type){

        //Lets check if we are doing special array handling
        $arrayHandling = false;
        $keyExploded   = explode('.', $key);
        if(count($keyExploded) > 1){
            $arrayHandling = true;
            $key           = array_shift($keyExploded);
            if($type == 'array'){
                $value = unserialize($value);
            }
        }


        // First let's try to fetch the model, if it exists then we need to do an
        // Update not an insert
        $model = DatabaseConfigLoaderModel::where('key', $key)->where('group', $group);
        is_null($environment) ? $model->whereNull('environment') : $model->where('environment', $environment);
        is_null($package) ? $model->whereNull('package') : $model->where('package', $package);
        $model = $model->first();


        if(is_null($model)){

            //Check if we need to do special array handling
            if($arrayHandling){ // we are setting a subset of an array
                $array = array();
                self::buildArrayPath($keyExploded, $value, $array);
                $value = serialize($array);
                $type  = 'array';
            }

            DatabaseConfigLoaderModel::create(
                array(
                     'environment' => $environment,
                     'package'     => $package,
                     'group'       => $group,
                     'key'         => $key,
                     'value'       => $value,
                     'type'        => $type,
                ));

        } else{

            //Check if we need to do special array handling
            if($arrayHandling){ // we are setting a subset of an array
                $array = array();
                self::buildArrayPath($keyExploded, $value, $array);

                //do we need to merge?
                if($model->type == 'array'){
                    $array = array_replace_recursive(unserialize($model->value), $array);
                }
                $value = serialize($array);

                $type = 'array';
            }

            $model->value = $value;
            $model->type  = $type;
            $model->save();
        }
    }

    protected static function buildArrayPath($map, $value, &$array){
        $key = array_shift($map);
        if(count($map) !== 0){
            $array[$key] = array();
            self::buildArrayPath($map, $value, $array[$key]);
        } else{
            $array[$key] = $value;
        }
    }
}