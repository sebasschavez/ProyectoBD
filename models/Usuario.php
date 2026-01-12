<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'Usuario';
    protected $primaryKey = 'ID_Usuario';
    public $timestamps = false;

    protected $fillable = [
        'Nombre_Usuario',
        'Contrasena',
        'ID_Empleado',
        'Rol',
        'Activo'
    ];

    protected $hidden = ['Contrasena'];

    protected $casts = [
        'Activo' => 'boolean',
        'Fecha_Creacion' => 'datetime'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'ID_Empleado');
    }

    public function scopeActivos($query)
    {
        return $query->where('Activo', 1);
    }

    public function esAdmin()
    {
        return $this->Rol === 'admin';
    }

    public function esVendedor()
    {
        return $this->Rol === 'vendedor';
    }
}