<?php

namespace App\mapping;

use Illuminate\Database\Eloquent\Model;

class ms_odoo_map_uom_product_model extends Model
{
    protected $table = 'ms_odoo_map_uom_product';
    protected $id ='id';
    protected $fillable=[ 
                        'id',  
                        'product_id',  
                        'product_code',    
                        'uom_id',  
                        'uom_short_name',  
                        'uom_long_name'
                       ];  
    
    public $timestamps=False;
}
 