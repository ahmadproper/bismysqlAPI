<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class pc_purchase_order_model extends Model
{
    protected $table = 'pc_purchase_order';
    protected $id ='No_PO';
    protected $fillable=[ 
                        'No_PO',              
                        'No_PR',            
                        'Tgl_PO',             
                        'Kode_Principal',     
                        'Kode_Divisi_Produk', 
                        'Jenis_Beli',         
                        'Tgl_Dibutuhkan',     
                        'Kode_Cabang_Tujuan', 
                        'Alamat_Kirim',       
                        'TOP',                
                        'Diskon',             
                        'SubTotal',           
                        'PPN_SubTotal',       
                        'PPN',                
                        'PPN_BM',             
                        'Materai',            
                        'Total',              
                        'Franco',             
                        'Status',             
                        'User_ID',            
                        'Time_Stamp',         
                        'Pengali'                               
                        ];                       
    public $timestamps='Time_Stamp';
}