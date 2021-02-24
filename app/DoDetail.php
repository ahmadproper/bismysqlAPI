<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoDetail extends Model
{
    protected $table='in_delivery_subdetail';
    protected $id='No_Delivery';
    protected $fillable = [
        'No_Delivery' ,      
        'Kode_GudanG'  ,     
        'Kode_Barang'   ,    
        'No_Batch'       ,   
        'Jumlah'          ,  
        'Satuan'           , 
        'Kadaluarsa'        ,
        'Terima'            ,
        'ID_ProGRam_Promosi'
    ];
    public $timestamps=false;
}
