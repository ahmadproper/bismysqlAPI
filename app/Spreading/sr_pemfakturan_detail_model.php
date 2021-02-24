<?php

namespace App\Spreading;

use Illuminate\Database\Eloquent\Model;

class sr_pemfakturan_detail_model extends Model
{
    protected $table   = 'sr_pemfakturan_detail';
    #protected $id       =['No_Peminjaman'];
    protected $fillable = [  'No_Pemfakturan',     
                             'No_Detail',          
                             'Kode_Barang',        
                             'No_Batch',           
                             'Satuan',             
                             'Jumlah',             
                             'Kadaluarsa',         
                             'Harga_Barang',       
                             'Diskon_Barang',      
                             'Diskon_Tambahan',    
                             'ID_Program_Promosi', 
                             'ID_Program_Discount',
                             'ID_Program_Voucher' 
                          ];           
    public $timestamps = false;
}
