<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class in_kartu_stok_detail_model extends Model
{
    protected $table = 'in_kartu_stok_detail';     
    #protected $id='No_Kertas_Kerja';   
    protected $fillable=
    [
    'Periode',            
    'No_Transaksi',       
    'Jenis_Transaksi',    
    'Tgl_Transaksi',      
    'Barang',             
    'Gudang',             
    'STATUS',             
    'Batch',              
    'Level_Asal',         
    'Net',                
    'Harga_Beli',         
    'Keterangan',         
    'ID_Program_Promosi', 
    'Expired',            
    'TimeStamp'] ;
    public $timestamps=false;            

}
