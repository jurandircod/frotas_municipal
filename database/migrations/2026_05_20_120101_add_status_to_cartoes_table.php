<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cartoes', function (Blueprint $table) {
            $table->enum('status', ['em_uso', 'inativo'])->default('inativo')->after('aumento_horimetro');
        });
    }

    public function down(): void
    {
        Schema::table('cartoes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};