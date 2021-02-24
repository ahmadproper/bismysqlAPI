<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sl_retur_barang_model extends Model
{
    protected $table = 'sl_retur_barang';     
    protected $id='No_BRB';   
    protected $fillable=
                        [
                            'No_BRB',       
                            'No_TBR',          
                            'No_Faktur',       
                            'No_Faktur_Pajak', 
                            'Tgl_Pajak',       
                            'Kode_Pelanggan',  
                            'Kode_Gudang',     
                            'Tgl_BRB',         
                            'Total_Harga',     
                            'Potongan',        
                            'Diskon_BRB',      
                            'PPN',             
                            'Materai',         
                            'Status',          
                            'Status_Tercetak', 
                            'Time_Stamp',      
                            'User_ID'
                        ];
    
    public $timestamps='Time_Stamp';  
        
}
