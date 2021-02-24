<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class in_stock_opname_selisih_model extends Model
{
    protected $table = 'in_stock_opname_selisih';     
    #protected $id='No_Kertas_Kerja';   
    protected $fillable=    
        [
        'No_Kertas_Kerja',
        'Kode_Barang',    
        'No_Batch',       
        'Kadaluarsa',     
        'Level',          
        'Jumlah',         
        'Referensi'
        ];                 
    public $timestamps=false;     
}
