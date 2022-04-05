<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('rate');
            $table->string('author_name');
            $table->string('author_avt')->nullable();
            $table->string('title')->nullale();
            $table->longText('content')->nullable();
            $table->json('img')->nullable();
            $table->string('created_at');
            $table->json('store_reply')->nullable();
            $table->json('store_reply_created_at')->nullable();
            $table->integer('number_like')->nullable();
            $table->integer('number_dislike')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
