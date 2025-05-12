<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('title');
            $table->text('konten');
            $table->text('clean')->nullable();
            $table->text('casefolding')->nullable();
            $table->text('normalization')->nullable();
            $table->text('tokenizing')->nullable();
            $table->text('stopword')->nullable();
            $table->text('stemming')->nullable();
            $table->string('bobot_label')->nullable();
            $table->enum('label',['positif','netral','negatif'])->nullable();
            $table->string('komen_name');
            $table->string('rate');
            $table->timestamps();

            $table->foreign('product_id')
                   ->references('id')
                   ->on('products')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};