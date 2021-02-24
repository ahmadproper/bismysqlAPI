<?php

namespace App\Returns;

use Illuminate\Database\Eloquent\Model;

class pc_retur_beli_model extends Model
{
protected $table ='pc_retur_beli';
protected $id    ='No_Retur_Beli';
protected $fillable =
                    ['No_Retur_Beli',
                    'No_Referensi',       
                    'Tgl_Retur_Beli',     
                    'Tgl_Kirim',          
                    'Status',             
                    'Kode_Gudang',        
                    'Kode_Principal',     
                    'Kode_Divisi_Produk', 
                    'Total_Pengajuan',    
                    'Total_Penggantian',  
                    'User_ID',            
                    'Time_Stamp',         
                    'Jenis_Retur',        
                    'Periode_Awal',       
                    'Periode_Akhir',      
                    'No_RGR',             
                    'No_NR'];              
    public $timestamps=false;
}
