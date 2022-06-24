<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meal_translations', function (Blueprint $table) {
          $table->id('id');
          $table->bigInteger('meal_id')->unsigned();
          $table->string('locale')->index();
        

          $table->string('title');
          $table->text('description');

          $table->foreignId('category_id')->nullable();
          $table->json('tag_ids')->nullable();
          $table->json('ingredient_ids')->nullable();
        

          $table->unique(['meal_id', 'locale']);
          $table->foreign('meal_id')->references('id')->on('meals')->onDelete('cascade');
          $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meal_translations');
    }
};
