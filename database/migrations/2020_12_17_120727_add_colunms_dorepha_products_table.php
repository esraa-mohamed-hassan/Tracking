<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColunmsDorephaProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dorepha_products', function (Blueprint $table) {
            $table->string('concentration')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('texture')->nullable();
            $table->string('skin_type')->nullable();
            $table->string('area_of_apply')->nullable();
            $table->string('brand_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dorepha_products', function (Blueprint $table) {});
    }
}
