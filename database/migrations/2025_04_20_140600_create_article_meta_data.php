<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleMetaData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_meta_data', function (Blueprint $table) {
            $table->id(); // Standard auto-incrementing ID
            $table->string('publisherId', 255);
            $table->string('title', 255)->nullable();
            $table->string('author', 255)->nullable();
            $table->string('keywords', 255)->nullable();
            $table->text('description')->nullable(); // Changed to text for longer content
            $table->longText('content');
            $table->string('og_image', 255)->nullable();
            $table->string('og_url', 255)->nullable();
            $table->string('namechannel', 255)->nullable();
            $table->string('cateslug', 255)->nullable();
            $table->text('summarize')->nullable();
            $table->text('audio')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index('publisherId');
            $table->index('cateslug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_meta_data');
    }
}
