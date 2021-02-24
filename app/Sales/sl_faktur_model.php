<?php

namespace App\Sales;

use Illuminate\Database\Eloquent\Model;

class sl_faktur_model extends Model
{
    protected $table='sl_faktur';
    protected $id=['No_Faktur'];
    protected $fillable =
                   ['No_Faktur',         
                    'Kode_Jenis_Jual',    
                    'Kode_Divisi_Produk', 
                    'Kode_Pelanggan',     
                    'No_Referensi',       
                    'No_Delivery',        
                    'No_Kontra_Bon',      
                    'Tgl_Faktur',         
                    'Tgl_Permintaan_Kiri',
                    'Kode_Personil',      
                    'Kode_Rayon_Kolektor',
                    'PPN',                
                    'Materai',            
                    'Diskon',             
                    'Potongan',           
                    'Total_Tagihan',      
                    'Total_Bayar',        
                    'Status_Lunas',       
                    'Status_Print_Terima',
                    'Tgl_Jatuh_Tempo',    
                    'Tgl_Pembayaran',     
                    'Status_Tercetak',    
                    'Exclusive',          
                    'Time_Stamp',         
                    'User_ID'];       
    public $timestamps = false;                       

}
