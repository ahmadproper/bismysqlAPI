<?php

namespace App\Purchase;

use Illuminate\Database\Eloquent\Model;

class pc_barang_datang_model extends Model
{
    protected $table   ='pc_barang_datang';
    protected $id      =['No_BD'];
    protected $fillable=[
                    'No_BD',           
                    'Tgl_BD',          
                    'No_PO',           
                    'No_BPB',          
                    'No_BPB_Principal',
                    'No_DO',           
                    'Kode_Principal',  
                    'Kode_Gudang',     
                    'Kurir',           
                    'Status',          
                    'Jenis_Pengiriman',
                    'User_ID',         
                    'Time_Stamp'];  
    
    public $timestamps = false;          
	// Test 2020-11-10 14:10
	//

}
