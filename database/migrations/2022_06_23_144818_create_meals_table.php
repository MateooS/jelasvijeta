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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();

            //$table->foreignId('tag_ids')->references('id')->on('tags')->json();
            //$table->foreignId('ingredient_ids')->references('id')
            //->on('ingredients')->json();

            /* TODO REMOVE THE NULLABLE FIELD FOR THESE 3! */
            //$table->foreignId('category_id')->nullable();
            //$table->text('description')->nullable();
            //$table->json('tag_ids')->nullable();
            //$table->json('ingredient_ids')->nullable();

            $table->timestamps();
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
        Schema::dropIfExists('meals');
    }
};
