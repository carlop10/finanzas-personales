<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'presupuesto_mensual'
    ];

    protected $casts = [
        'presupuesto_mensual' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }
}
