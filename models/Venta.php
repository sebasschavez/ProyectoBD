<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'Venta';
    protected $primaryKey = 'ID_Venta';
    public $timestamps = false;

    protected $fillable = [
        'Venta_Fecha',
        'Total',
        'ID_Empleado'
    ];

    protected $casts = [
        'Total' => 'decimal:2',
        'Venta_Fecha' => 'datetime'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'ID_Empleado');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'ID_Venta');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('Venta_Fecha', today());
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('Venta_Fecha', [$inicio, $fin]);
    }

    public function getTotalFormateadoAttribute()
    {
        return formatear_dinero($this->Total);
    }

    public function getFechaFormateadaAttribute()
    {
        return formatear_fecha($this->Venta_Fecha);
    }

    public function cantidadItems()
    {
        return $this->detalles()->count();
    }
}