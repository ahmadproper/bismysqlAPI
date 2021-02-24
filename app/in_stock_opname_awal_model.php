<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class in_stock_opname_awal_model extends Model
{
    protected $table = 'in_stock_opname_awal';     
    #protected $id='No_Kertas_Kerja';   
    protected $fillable=
    [
        'No_Kertas_Kerja',
        'Kode_Barang',    
        'No_Batch',       
        'Kadaluarsa',     
        'Level',          
        'Jumlah',         
        'booked'
    ];      
    public $timestamps=false;     
}
