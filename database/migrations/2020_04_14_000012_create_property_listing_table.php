<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyListingTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'property_listing';

    /**
     * Run the migrations.
     * @table property_listing
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('property_listing_id');
            $table->integer('start_date');
            $table->integer('end_date');
            $table->integer('property_id');
            $table->decimal('price', 6, 2);
            $table->tinyInteger('reccurring')->default('0');
            $table->tinyInteger('inactive')->default('0');

            $table->index(["property_id"], 'property_id_idx');

            $table->unique(["property_listing_id"], 'property_listing_id_UNIQUE');


            $table->foreign('property_id', 'property_id_idx')
                ->references('property_id')->on('properties')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
