<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function(Blueprint $table)
        {
            $table->index(['brand_value']);
            $table->index(['category']);
            $table->index(['concentration']);
            $table->index(['size']);
            $table->index(['color']);
            $table->index(['texture']);
            $table->index(['skin_type']);
            $table->index(['area_of_apply']);
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table)
        {
            $table->dropIndex(['products_brand_value_index']);
            $table->dropIndex(['products_category_index']);
            $table->dropIndex(['products_concentration_index']);
            $table->dropIndex(['products_size_index']);
            $table->dropIndex(['products_color_index']);
            $table->dropIndex(['products_texture_index']);
            $table->dropIndex(['products_skin_type_index']);
            $table->dropIndex(['products_area_of_apply_index']);
            $table->dropIndex(['products_name_index']);
        });
    }
}
