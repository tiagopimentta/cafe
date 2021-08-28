<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarrafasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garrafas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->smallInteger('capacidade_total')->default('1000');
            $table->smallInteger('quantidade_atual')->default('0');
            $table->smallInteger('capacidade_xicara')->default('200');
            $table->smallInteger('limite_cafe')->default('3');
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
        Schema::dropIfExists('garrafas');
    }
}
