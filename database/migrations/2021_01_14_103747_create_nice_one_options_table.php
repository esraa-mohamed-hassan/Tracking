<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNiceOneOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('niceone_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('niceone_pro_id');
            $table->foreign('niceone_pro_id')->references('id')->on('niceone_products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('type');
            $table->string('sku')->nullable();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->decimal('price_after_discount', 10, 2);
            $table->integer('discount_ratio');
            $table->string('currency');
            $table->string('hex_color')->nullable();
            $table->integer('active')->nullable();
            $table->integer('stock')->nullable();
            $table->string('stock_quantity')->nullable();
            $table->integer('option_id')->nullable();
            $table->integer('product_option_id')->nullable();
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
        Schema::table('niceone_options', function (Blueprint $table) {
            $table->dropForeign(['niceone_pro_id']);
            $table->dropColumn(['niceone_pro_id']);
        });
    }
}
