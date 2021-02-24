<?php

namespace App\Purchase;

use Illuminate\Database\Eloquent\Model;

class pc_barang_datang_harga_model extends Model
{
    protected $table    ='pc_barang_datang_harga';    
    protected $fillable = [
                          'No_BD',               
                          'No_Detail',  
                          'Kode_Barang',    
                          'Satuan',      
                          'Jumlah',  
                          'No_Batch',    
                          'Kadaluarsa',  
                          'Status_Barang'  
                ];  
    
    public $timestamps = false;          

}
