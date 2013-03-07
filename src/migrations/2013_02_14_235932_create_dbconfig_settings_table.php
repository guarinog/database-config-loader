<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDbconfigSettingsTable
 *
 * Schema
 */

class CreateDbconfigSettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
        /** @var \Illuminate\Database\Schema\Blueprint $table */
		Schema::create('dbconfig_settings', function($table)
		{

			$table->increments('id');
            $table->string('package')->nullable();
            $table->string('group')->default('config');
            $table->string('key');
            $table->string('value')->nullable();
            $table->string('type');
            $table->string('environment')->nullable();
            $table->unique(array('package', 'key', 'environment'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dbconfig_settings');
	}

}