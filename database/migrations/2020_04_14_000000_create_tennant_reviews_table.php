<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTennantReviewsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tennant_reviews';

    /**
     * Run the migrations.
     * @table tennant_reviews
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('trs_id');
            $table->integer('trs_booking_id');
            $table->integer('trs_tennant_id');
            $table->integer('trs_reviewer_id');
            $table->integer('trs_score');
            $table->string('trs_review', 245)->nullable()->default(null);
            $table->tinyInteger('trs_inactive')->default('0');
            $table->integer('trs_submitted_at');
            $table->tinyInteger('trs_edited')->default('0');
            $table->integer('trs_edited_at')->nullable()->default(null);
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
