<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWishlistTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'wishlist';

    /**
     * Run the migrations.
     * @table wishlist
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('wishlist_id');
            $table->integer('wishlist_userID')->nullable()->default(null);
            $table->integer('wishlist_propertyID')->nullable()->default(null);
            $table->string('wishlist_propertyTitle', 100)->nullable()->default(null);
            $table->string('wishlist_propertyAddress', 100)->nullable()->default(null);
            $table->tinyInteger('wishlist_inactive')->nullable()->default(null);
            $table->integer('wishlist_createdAt')->nullable()->default(null);
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
