<?php

namespace App\stockopname;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $table='in_stock_opname';
    protected $id='no_kertas_kerja';
    protected $fillable =[
        'no_kertas_kerja'     ,
        'Kode_Principal'      ,
        'Kode_Divisi_Produk'  ,
        'Kode_Gudang'         ,
        'tanggal',
        'Status_Barang'       ,
        'status',
        'User_ID'   ,          
        'time_stamp'          
    ];
    public $timestamps = false;

}
