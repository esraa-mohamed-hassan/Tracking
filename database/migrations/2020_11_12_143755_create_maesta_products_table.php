<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaestaProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maesta_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 10,2);
            $table->decimal('price_after_discount', 10,2);
            $table->text('description');
            $table->string('pro_type')->nullable();
            $table->string('pro_status')->nullable();
            $table->integer('visibility')->nullable();
            $table->string('url');
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
        Schema::dropIfExists('maesta_products');
    }
}
