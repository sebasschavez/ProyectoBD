<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'Producto';
    protected $primaryKey = 'ID_Producto';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Precio_Venta',
        'Cantidad_Stock',
        'Tipo_Producto',
        'Fecha_Caducidad',
        'Requiere_Refrigeracion',
        'Marca',
        'Contenido_Neto'
    ];

    protected $casts = [
        'Precio_Venta' => 'decimal:2',
        'Cantidad_Stock' => 'integer',
        'Requiere_Refrigeracion' => 'boolean',
        'Fecha_Caducidad' => 'date'
    ];


    public function proveedores()
    {
        return $this->belongsToMany(
            Proveedor::class,
            'Suministra',
            'ID_Producto',
            'ID_Proveedor'
        )->withPivot('Costo_de_Compra');
    }


    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'ID_Producto');
    }


    public function scopeStockBajo($query, $limite = 10)
    {
        return $query->where('Cantidad_Stock', '<', $limite);
    }

    public function scopePerecederos($query)
    {
        return $query->where('Tipo_Producto', 'Perecedero');
    }

    public function scopeAbarrotes($query)
    {
        return $query->where('Tipo_Producto', 'Abarrote');
    }

    public function scopeConStock($query)
    {
        return $query->where('Cantidad_Stock', '>', 0);
    }


    public function getPrecioFormateadoAttribute()
    {
        return formatear_dinero($this->Precio_Venta);
    }

    public function tieneStockBajo()
    {
        return $this->Cantidad_Stock < 10;
    }
}