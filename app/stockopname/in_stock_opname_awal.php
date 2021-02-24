<?php

namespace App\stockopname;

use Illuminate\Database\Eloquent\Model;

class in_stock_opname_awal extends Model
{
    
    protected $table='in_stock_opname_awal';
    #protected $id=['No_Kertas_Kerja','Kode_Barang','Level'];
    protected $fillable =[
        'No_Kertas_Kerja',
        'Kode_Barang',
        'No_Batch',
        'Kadaluarsa',
        'Level',
        'Jumlah',
        'booked'        
    ];
    public $timestamps = false;
}
