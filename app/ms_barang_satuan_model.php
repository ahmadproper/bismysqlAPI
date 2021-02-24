<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ms_barang_satuan_model extends Model
{
    protected $table = 'ms_barang_satuan';     
    protected $id='Kode_Barang';
    protected $fillable=[ 
                        'Kode_Barang',    
                        'Satuan',         
                        'Level',          
                        'Konversi',       
                        'Jual',           
                        'Retur',          
                        'Spreading',      
                        'Berat',          
                        'Panjang',        
                        'Lebar',          
                        'Tinggi',        
                        'Harga_Beli',     
                        'Harga_TAC',      
                        'Harga_Jual',     
                        'Harga_Spreading'                   
                       ]; 
    public $timestamps = False;
}
