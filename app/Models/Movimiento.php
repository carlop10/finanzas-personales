<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $table = 'movimientos';

    protected $fillable = [
        'user_id',
        'billetera_id',
        'categoria_id',
        'tipo',
        'monto',
        'horas_trabajadas',
        'kilometros_recorridos',
        'fecha',
        'descripcion'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'horas_trabajadas' => 'decimal:2',
        'kilometros_recorridos' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billetera()
    {
        return $this->belongsTo(Billetera::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
