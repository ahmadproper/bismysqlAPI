<?php

namespace App\Purchase;

use Illuminate\Database\Eloquent\Model;

class pc_purchase_order_detail_model extends Model
{
    protected $table    ='pc_purchase_order_detail';
    protected $id       =['No_PO','No_Detail','Kode_Barang'];
    protected $fillable = [
                            'No_PO',                        
                            'No_Detail',  
                            'ID_Program_Diskon_Principal',  
                            'Kode_Barang',  
                            'Satuan',      
                            'SSL_PR',  
                            'Jumlah_Stok',     
                            'GOO',     
                            'GIT',  
                            'Jumlah',  
                            'Harga_Barang',  
                            'Diskon_Barang',  
                            'Diskon_Tambahan',  
                            'ID'];
        public $timestamps = false;     
    }
