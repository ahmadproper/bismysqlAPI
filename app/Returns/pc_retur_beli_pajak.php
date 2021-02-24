<?php

namespace App\Returns;

use Illuminate\Database\Eloquent\Model;

class pc_retur_beli_pajak extends Model
{   
    protected $table ='pc_retur_beli_pajak';
    protected $fillable =
                         ['No_Retur_Beli',
                         'Kode_Barang',     
                         'No_Batch',        
                         'Satuan_Referensi',
                         'No_Faktur_Pajak', 
                         'Tgl_Faktur_Pajak',
                         'Jumlah',          
                         'Satuan',          
                         'Harga',           
                         'Diskon',          
                         'Diskon_Tambahan', 
                         'Diskon_Faktur',   
                         'Diskon_COD',      
                         'Status_Barang'];   
    public $timestamps = false;

}
