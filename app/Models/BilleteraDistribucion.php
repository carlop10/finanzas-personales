<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BilleteraDistribucion extends Model
{
    protected $table = 'billetera_distribuciones';

    protected $fillable = [
        'billetera_origen_id',
        'billetera_destino_id',
        'porcentaje',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
    ];

    public function origen()
    {
        return $this->belongsTo(Billetera::class, 'billetera_origen_id');
    }

    public function destino()
    {
        return $this->belongsTo(Billetera::class, 'billetera_destino_id');
    }
}
