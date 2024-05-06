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
        Schema::table('products', function (Blueprint $table) {
            $table->text('sort_description')->nullable()->after('description');
            $table->text('shipping_return')->nullable()->after('sort_description');
            $table->text('related_product')->nullable()->after('shipping_return');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sort_description');
            $table->dropColumn('shipping_return');
            $table->dropColumn('related_product');

        });
    }
};
