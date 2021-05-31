<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexsUpdateGoldenscentPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('update_goldenscent_price', function(Blueprint $table)
        {
            $table->index(['sku']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('update_goldenscent_price', function (Blueprint $table)
        {
            $table->dropIndex(['sku']);
            $table->dropIndex(['created_at']);
        });
    }
}
