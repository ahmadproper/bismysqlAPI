<?php

namespace App\Returns;

use Illuminate\Database\Eloquent\Model;

class sl_terima_barang_retur_detail_model extends Model
{
    protected $table ='sl_terima_barang_retur_detail';
    protected $fillable =
                         [
                         'No_TBR',
                         'Kode_Barang',    
                         'Satuan',      
                         'Batch',       
                         'Jumlah',  
                         'Jumlah_Diterima',  
                         'Tgl_Kadaluarsa',  
                         'No_Faktur',            
                         'No_Faktur_Pajak',      
                         'Kode_Personil',  
                         'Jenis_Retur',                                                
                         'Penggantian',  
                         'Keterangan',  
                         'Alasan_Tolak',  
                         'Status_Barang',  
                         'P3BO',    
                         'Pengajuan_P3BO',  
                         'Harga_Barang',  
                         'Diskon_Barang',  
                         'Diskon_Tambahan',  
                         'Potongan',  
                         'Cash_Diskon',  
                         'Pembebanan_PPN',  
                         'Tgl_Pajak'];   
    public $timestamps = false;
}
