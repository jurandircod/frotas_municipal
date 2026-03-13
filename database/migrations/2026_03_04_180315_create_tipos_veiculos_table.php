<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposVeiculosTable extends Migration
{
    public function up()
    {
        Schema::create('tipos_veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipos_veiculos');
    }
}