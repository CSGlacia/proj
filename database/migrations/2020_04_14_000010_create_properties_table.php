<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'properties';

    /**
     * Run the migrations.
     * @table properties
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('property_id');
            $table->integer('property_user_id');
            $table->string('property_address', 100);
            $table->integer('property_beds');
            $table->integer('property_baths');
            $table->integer('property_cars');
            $table->string('property_desc');
            $table->tinyInteger('property_inactive')->default('0');
            $table->string('property_title', 45);
            $table->decimal('property_lat', 9, 6);
            $table->decimal('property_lng', 9, 6);
            $table->tinyInteger('property_always_list')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
