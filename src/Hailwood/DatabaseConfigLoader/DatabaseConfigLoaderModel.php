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
        // First let's try to fetch the model, if it exists then we need to do an
        // Update not an insert
        $model = DatabaseConfigLoaderModel::where('key', $key)->where('group', $group);
        is_null($environment) ? $model->whereNull('environment') : $model->where('environment', $environment);
        is_null($package) ? $model->whereNull('package') : $model->where('package', $package);
        $model = $model->first();


        if(is_null($model)){
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
            $model->value = $value;
            $model->type  = $type;
            $model->save();
        }
    }
}