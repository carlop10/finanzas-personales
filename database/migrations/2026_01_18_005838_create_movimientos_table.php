<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billetera_id')
                ->constrained('billeteras')
                ->cascadeOnDelete();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->cascadeOnDelete();

            $table->enum('tipo', ['ingreso', 'gasto']);
            $table->decimal('horas_trabajadas', 5, 2)->nullable();
            $table->decimal('kilometros_recorridos', 7, 2)->nullable();
            $table->decimal('monto', 12, 2);
            $table->date('fecha');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
