<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeiculosTable extends Migration
{
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 10)->unique();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 150)->nullable();
            $table->smallInteger('ano')->nullable();
            $table->string('cor', 50)->nullable();
            $table->unsignedBigInteger('tipo_veiculo_id')->nullable();
            $table->string('combustivel', 30)->nullable(); // gasolina, etanol, diesel, flex, eletrico...
            $table->decimal('km_atual', 10, 1)->default(0);
            $table->string('status', 30)->default('ativo'); // ativo, manutencao, inativo...
            $table->timestamps();

            $table->foreign('tipo_veiculo_id')->references('id')->on('tipos_veiculos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('veiculos');
    }
}