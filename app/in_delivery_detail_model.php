<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class in_delivery_detail_model extends Model
{
    protected $table = 'in_delivery_detail';        
    protected $fillable= [
                        'No_Delivery',       
                        'Kode_Barang',       
                        'Jumlah',            
                        'Satuan',            
                        'Prepared',          
                        'ID_Program_Promosi'
                         ];
    public $timestamps=False;
}
