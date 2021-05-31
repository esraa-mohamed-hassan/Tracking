<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ScrappingLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrapping_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('line');
            $table->string('file');
            $table->string('dir');
            $table->string('function');
            $table->string('class');
            $table->string('trait');
            $table->string('method');
            $table->string('namespace');
            $table->string('subject');
            $table->longText('msgbody');
            $table->dateTime('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scrapping_logs');
    }
}
