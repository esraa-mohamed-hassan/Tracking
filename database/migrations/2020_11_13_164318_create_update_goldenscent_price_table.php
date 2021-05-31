<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateGoldenscentPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('update_goldenscent_price', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->decimal('price', 10,2);
            $table->decimal('price_after_discount', 10,2);
            $table->timestamp('last_update');
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
        Schema::dropIfExists('update_goldenscent_price');
    }
}
