<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sl_retur_barang_detail_model extends Model
{
    protected $table = 'sl_retur_barang_detail';     
    #protected $id='No_Kertas_Kerja';   
    protected $fillable=
    [
        'No_BRB',         
        'Kode_Barang',    
        'Satuan',         
        'No_Batch',       
        'Jumlah',         
        'Harga_Barang',   
        'HNA',            
        'Diskon_Barang',  
        'Diskon_Tambahan',
        'HNADisc',        
        'Tgl_Kadaluarsa', 
        'Jenis_Retur',    
        'Kode_Personil'
    ];
    public $timestamps=false;  
}
