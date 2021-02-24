<?php

namespace App\Spreading;

use Illuminate\Database\Eloquent\Model;

class sr_pemfakturan_model extends Model
{
    protected $table    = 'sr_pemfakturan';
    protected $id       = ['No_Peminjaman'];
    protected $fillable = [
                            'No_Pemfakturan',      
                            'ID_Spreading',        
                            'Kode_Jenis_Jual',     
                            'Kode_Divisi_Produk',  
                            'Tanggal_Pemfakturan', 
                            'Posted',              
                            'Kode_Pelanggan',      
                            'PPN',                 
                            'TOP',                 
                            'Diskon',              
                            'Potongan',            
                            'Total_Harga',         
                            'Exclusive',           
                            'Time_Stamp',          
                            'User_ID'
                          ];       
    
    public $timestamps = false;

}
