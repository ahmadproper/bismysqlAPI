<?php

namespace App\mapping;

use Illuminate\Database\Eloquent\Model;

class sr_peminjaman_mapping_odoo_model extends Model
{
    protected $table ='sr_peminjaman_mapping_odoo';     
    protected $id    ='picking_id';
    protected $fillable=[
                        'picking_id',
                        'No_Peminjaman',
                        'No_Delivery',                                 
                        'Time_Stamp'];   
    public $timestamps=False;
}
