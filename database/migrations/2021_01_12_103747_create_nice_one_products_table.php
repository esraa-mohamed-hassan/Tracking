<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNiceOneProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('niceone_products', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('sku')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar');
            $table->text('description_en');
            $table->decimal('price', 10, 2);
            $table->decimal('price_after_discount', 10, 2);
            $table->integer('discount_ratio');
            $table->string('currency');
            $table->string('url_en');
            $table->string('url_ar');
            $table->string('brand_value')->nullable();
            $table->string('category')->nullable();
            $table->string('concentration')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('texture')->nullable();
            $table->string('skin_type')->nullable();
            $table->string('area_of_apply')->nullable();
            $table->string('stock_quantity')->nullable();
            $table->string('status');
            $table->text('tags')->nullable();
            $table->enum('pro_status',array('pending','done'))->default('pending');
            $table->timestamp('last_update')->nullable();
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
}
