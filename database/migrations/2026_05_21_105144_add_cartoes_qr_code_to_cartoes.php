<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cartoes', function (Blueprint $table) {

            // remove a antiga se quiser
            if (Schema::hasColumn('cartoes', 'cartao_qr_code')) {
                $table->dropColumn('cartao_qr_code');
            }

            $table->longText('cartao_qr_retirada')->nullable();
            $table->longText('cartao_qr_entrega')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('cartoes', function (Blueprint $table) {
            $table->dropColumn([
                'cartao_qr_retirada',
                'cartao_qr_entrega',
            ]);
        });
    }
};