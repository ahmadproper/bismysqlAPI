<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class in_stock_opname_model extends Model
{
    protected $table = 'in_stock_opname';     
    protected $id='No_Kertas_Kerja';   
    protected $fillable=
    [
    'No_Kertas_Kerja',   
    'Kode_Principal',    
    'Kode_Divisi_Produk',
    'Kode_Gudang',       
    'Tanggal',           
    'Status_Barang',     
    'Status',            
    'User_ID',           
    'Time_Stamp'];      
    public $timestamps='Time_Stamp';          
    
}
