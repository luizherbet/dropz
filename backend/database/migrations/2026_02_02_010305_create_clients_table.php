<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de clientes (Clientes no MER).
     * Um cliente pode ter várias demandas (relacionamento 1:N).
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {

            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->boolean('avisar_por_email')->default(true);
            $table->string('whatsapp')->nullable();
            $table->boolean('avisar_por_whatsapp')->default(false);
            $table->text('observacoes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
