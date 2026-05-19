<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retiradas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cartao_id')
                ->nullable()
                ->constrained('cartoes')
                ->nullOnDelete();

            $table->foreignId('ferramenta_id')
                ->nullable()
                ->constrained('ferramentas')
                ->nullOnDelete();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('retirada_autorizada_por', 100)->nullable();
            $table->string('entrega_autorizada_por', 100)->nullable();

            $table->dateTime('datahora_retirada')->nullable();
            $table->dateTime('datahora_entrega')->nullable();

            $table->string('categoria', 50);
            $table->string('nome_generico', 150);
            $table->enum('status', ['pendente','pendente entrega','cancelar entrega', 'retirado', 'cancelado', 'negado', 'entregue'])
                ->default('pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retiradas');
    }
};
