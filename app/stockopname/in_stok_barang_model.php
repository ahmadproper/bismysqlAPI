<?php

namespace App\stockopname;

use Illuminate\Database\Eloquent\Model;

class in_stok_barang_model extends Model
{
    protected $table='in_stok_barang';
    #protected $id='no_kertas_kerja';
    protected $fillable =
    [
    'Kode_Barang',
    'Kode_Gudang',
    'No_Batch',   
    'STATUS',     
    'Level',      
    'Stok',       
    'Kadaluarsa'];
    public $timestamps=false;    

}
