<?php

namespace App\mapping;

use Illuminate\Database\Eloquent\Model;

class sr_pemfakturan_mapping_odoo_model extends Model
{
    protected $table='sr_pemfakturan_mapping_odoo';
    protected $id       =['sales_id'];
    protected $fillable =[
                         'sales_id',
                         'No_Pemfakturan',                                   
                         'Time_Stamp'          
                          ];           
    public $timestamps = false;
}
