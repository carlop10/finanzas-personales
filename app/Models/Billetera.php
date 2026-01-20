<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billetera extends Model
{
    protected $table = 'billeteras';

    protected $fillable = [
        'user_id',
        'nombre',
        'saldo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function distribuciones()
    {
        return $this->hasMany(BilleteraDistribucion::class, 'billetera_origen_id');
    }

    public function distribucionesEntrantes()
    {
        return $this->hasMany(BilleteraDistribucion::class, 'billetera_destino_id');
    }
}
