<?php
namespace App\Returns;

use Illuminate\Database\Eloquent\Model;

class pc_retur_beli_detail_model extends Model
{
    protected $table='pc_retur_beli_detail';    
    protected $fillable = [
                            'No_Retur_Beli',
                            'Kode_Barang',        
                            'No_Batch',           
                            'Kadaluarsa',         
                            'Stok',               
                            'Satuan_Stok',        
                            'Pengajuan',          
                            'Satuan_Pengajuan',   
                            'Persetujuan',        
                            'Satuan_Persetujuan', 
                            'Penggantian',        
                            'Satuan_Penggantian', 
                            'Pengembalian',       
                            'Satuan_Pengembalian',
                            'SubTotal',           
                            'Keterangan',         
                            'Status_Barang'      
                           ];              
    public $timestamps=false;
}
