<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sl_surat_pesanan_detail_model extends Model
{
    protected $table = 'sl_surat_pesanan_detail';    
    protected $fillable=[ 
                        'No_SP',                  
                        'No_Detail',              
                        'Kode_Barang',            
                        'Satuan',                 
                        'Jumlah',                 
                        'Satuan_Terpenuhi',       
                        'Terpenuhi',              
                        'Harga_Barang',           
                        'Harga_Awal',             
                        'Diskon_Barang_Standar',  
                        'Diskon_Barang_Tambahan', 
                        'Diskon_Barang',          
                        'Diskon_Tertera_Standar', 
                        'Diskon_Tertera_Tambahan',
                        'Diskon_Tertera',         
                        'ID_Program_Diskon',      
                        'ID_Program_Promosi',     
                        'ID_Program_Voucher' 
                        ];                       
    public $timestamps = False;
}
