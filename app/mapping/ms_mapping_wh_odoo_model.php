<?php

namespace App\mapping;

use Illuminate\Database\Eloquent\Model;

class ms_mapping_wh_odoo_model extends Model
{
    protected $table ='ms_mapping_wh_odoo';     
    protected $id    ='kode_gudang';
    protected $fillable=[
                        'kode_gudang',
                        'nama_gudang',
                        'wh_id',      
                        'wh_code',    
                        'wh_name'];   
    public $timestamps=False;
}
