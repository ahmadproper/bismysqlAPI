<?php

namespace App\mapping;

use Illuminate\Database\Eloquent\Model;

class in_stock_opname_blocking_model extends Model
{
    protected $table ='in_stock_opname_blocking';     
    protected $id    ='adjustment_id';
    protected $fillable=[
                        'adjustment_id',      
                        'no_kkso',            
                        'location_id',        
                        'principal_id',       
                        'product_division_id',
                        'Kode_Gudang',        
                        'Kode_Divisi_Produk', 
                        'Status_Adjustment',  
                        'Tgl_Awal',           
                        'Tgl_Akhir'
                       ];   
     
}
