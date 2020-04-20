<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyReviewsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'property_reviews';

    /**
     * Run the migrations.
     * @table property_reviews
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('prs_id');
            $table->integer('prs_booking_id');
            $table->integer('prs_property_id');
            $table->integer('prs_reviewer_id');
            $table->integer('prs_score');
            $table->string('prs_review', 245)->nullable()->default(null);
            $table->tinyInteger('prs_inactive')->default('0');
            $table->integer('prs_submitted_at');
            $table->tinyInteger('prs_edited')->default('0');
            $table->integer('prs_edited_at')->nullable()->default(null);
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
