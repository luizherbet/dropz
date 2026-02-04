<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de demandas (Demandas no MER).
     * Pertence a um cliente (cliente_id = FK para clients.id).
     */
    public function up(): void
    {
        Schema::create('demands', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')
                ->constrained('clients')
                ->onDelete('cascade');

            $table->string('titulo');
            $table->string('prioridade')->default('normal');
            $table->string('setor')->nullable();
            $table->string('responsavel')->nullable();
            $table->string('quem_deve_testar')->nullable();
            $table->text('descricao_detalhada')->nullable();
            $table->string('midia')->nullable();
            $table->boolean('cobrada_do_cliente')->default(false);

            $table->decimal('valor_total', 10, 2)->nullable();
            $table->decimal('valor_pago', 10, 2)->default(0);

            $table->integer('tempo_estimado')->nullable();
            $table->integer('tempo_gasto')->nullable();

            $table->string('status')->default('backlog');
            $table->boolean('flag_retornou')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};
