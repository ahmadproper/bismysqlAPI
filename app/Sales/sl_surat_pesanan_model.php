<?php

namespace App\Sales;

use Illuminate\Database\Eloquent\Model;

class sl_surat_pesanan_model extends Model
{
    protected $table='sl_surat_pesanan';
    protected $id   = ['No_SP'];
    protected $fillable = ['No_SP',               
                           'No_Referensi',        
                           'Jenis_SP',            
                           'Tgl_SP',              
                           'Tgl_Permintaan_Kirim',
                           'Kode_Jenis_Jual',     
                           'Kode_Divisi_Produk',  
                           'Kode_Pelanggan',      
                           'Kode_Personil',       
                           'Status_SP',           
                           'TOP',                 
                           'Total_Harga',         
                           'PPN',                 
                           'Materai',             
                           'Diskon',              
                           'Potongan',            
                           'Status_Tercetak',     
                           'Banded',              
                           'Exclusive',           
                           'Time_Stamp',          
                           'User_ID',             
                           'No_SP_Manual',        
                           'Tgl_SP_Manual',       
                           'ED_SP_Manual'        
                           ];      
    public $timestamps = false;                             
}
