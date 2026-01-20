<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billetera_distribuciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billetera_origen_id')
                ->constrained('billeteras')
                ->cascadeOnDelete();
            $table->foreignId('billetera_destino_id')
                ->constrained('billeteras')
                ->cascadeOnDelete();
            $table->decimal('porcentaje', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billetera_distribuciones');
    }
};
