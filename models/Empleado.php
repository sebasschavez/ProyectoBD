<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'Empleado';
    protected $primaryKey = 'ID_Empleado';
    public $timestamps = false;

    protected $fillable = ['Nombre', 'Apellido', 'Puesto'];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'ID_Empleado');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'ID_Empleado');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->Nombre . ' ' . $this->Apellido;
    }
}
