<?php

namespace App\mapping;

use Illuminate\Database\Eloquent\Model;

class in_delivery_flag_wms_model extends Model
{
    protected $table = 'in_delivery_flag_wms';    
    protected $id='No_Delivery';
    protected $fillable=[
                        'No_Delivery',
                        'Flag_WMS',   
                        'picking_name'];                        
    public $timestamps = False;
}
