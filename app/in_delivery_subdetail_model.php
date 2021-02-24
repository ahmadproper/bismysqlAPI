<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class in_delivery_subdetail_model extends Model
{
    protected $table = 'in_delivery_subdetail';     
    protected $fillable=[ 
                        'No_Delivery',         
                        'Kode_Gudang',  
                        'Kode_Barang',
                        'No_Batch',  
                        'Jumlah',  
                        'Satuan',  
                        'Kadaluarsa',  
                        'Terima',  
                        'ID_Program_Promosi'  
                       ]; 
    public $timestamps = False;
}
