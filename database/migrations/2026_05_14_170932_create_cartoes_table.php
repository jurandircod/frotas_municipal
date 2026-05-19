<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartoes', function (Blueprint $table) {
            $table->id();

            $table->string('nome_veiculo', 100);
            $table->string('placa', 10)->unique();
            $table->string('numero_cartao', 50)->unique();
            $table->integer('horimetro')->default(0);
            $table->integer('aumento_horimetro')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartoes');
    }
};