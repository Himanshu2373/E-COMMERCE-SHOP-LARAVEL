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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->double('price',10,2);
            $table->double('compare_price',10,2)->nullable();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');
            $table->foreignId('sub_category_id')
                ->nullable()
                ->constrained('subcategories')
                ->onDelete('cascade');
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands')
                ->onDelete('cascade');
            $table->foreignId('color_id')
                ->nullable()
                ->constrained('colors')
                ->onDelete('cascade');
            $table->foreignId('size_id')
                ->nullable()
                ->constrained('sizes')
                ->onDelete('cascade');
            $table->enum('is_featured',['Yes','No'])->default('No');
            $table->string('sku');
            $table->string('barcode')->nullable();
            $table->enum('track_qty',['Yes','No'])->default('Yes');
            $table->integer('qty')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
