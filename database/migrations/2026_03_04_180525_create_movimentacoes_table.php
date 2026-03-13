<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimentacoesTable extends Migration
{
    public function up()
    {
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->time('hora')->nullable();
            $table->unsignedBigInteger('veiculo_id');
            $table->unsignedBigInteger('user_id');
            $table->string('tipo_combustivel', 30)->nullable();
            $table->decimal('km_inicial', 10, 1)->nullable();
            $table->decimal('km_final', 10, 1)->nullable();
            $table->decimal('km_rodado', 10, 1)->nullable(); // pode ser calculado na aplicação também
            $table->string('origem')->nullable();
            $table->string('destino')->nullable();
            $table->text('observacao')->nullable();
            $table->string('status', 30)->default('ativa'); // ativa, finalizada, cancelada etc
            $table->timestamps();

            $table->foreign('veiculo_id')->references('id')->on('veiculos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['data']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimentacoes');
    }
}