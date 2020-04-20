<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'bookings';

    /**
     * Run the migrations.
     * @table bookings
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('booking_id');
            $table->unsignedInteger('booking_userID')->nullable()->default(null);
            $table->unsignedInteger('booking_propertyID')->nullable()->default(null);
            $table->integer('booking_startDate')->default('1607731200');
            $table->integer('booking_endDate');
            $table->unsignedInteger('booking_persons')->nullable()->default(null);
            $table->integer('booking_paid')->nullable()->default(null);
            $table->tinyInteger('booking_inactive')->default('0');
            $table->tinyInteger('booking_property_reviewed')->default('0');
            $table->integer('booking_property_review_id')->nullable()->default(null);
            $table->tinyInteger('booking_tennant_reviewed')->default('0');
            $table->integer('booking_tennant_review_id')->nullable()->default(null);
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
