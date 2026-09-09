<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('canal', 32);
            $table->string('status', 32)->default('rascunho');
            $table->unsignedInteger('total_em_centavos')->default(0);
            $table->string('observacao', 500)->nullable();
            $table->timestamps();

            // Índice em coluna de filtro frequente. Sem isso, a listagem
            // do painel vira varredura de tabela assim que o volume cresce.
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
