<?php

namespace App\stockopname;

use Illuminate\Database\Eloquent\Model;

class StockOpnameMapping extends Model
{
    protected $table='in_stock_opname_mapping_header';
    protected $id='id';
    protected $fillable =[
        'id'           ,    
        'no_kertas_kerja',  
        'inventory_id',
        'created_at',
        'updated_at'
    ];
    #public $timestamps = false;
}
