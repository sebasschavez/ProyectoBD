<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'Proveedor';
    protected $primaryKey = 'ID_Proveedor';
    public $timestamps = false;

    protected $fillable = ['Nombre_Empresa', 'Email', 'Telefono'];

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'Suministra',
            'ID_Proveedor',
            'ID_Producto'
        )->withPivot('Costo_de_Compra');
    }
}