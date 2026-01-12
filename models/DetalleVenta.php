<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'Detalle_Venta';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'ID_Venta',
        'ID_Producto',
        'Cantidad_vendida',
        'Precio_en_Venta'
    ];

    protected $casts = [
        'Cantidad_vendida' => 'integer',
        'Precio_en_Venta' => 'decimal:2'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'ID_Venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'ID_Producto');
    }

    public function getSubtotalAttribute()
    {
        return $this->Cantidad_vendida * $this->Precio_en_Venta;
    }
}