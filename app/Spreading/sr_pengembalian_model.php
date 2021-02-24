<?php

namespace App\Spreading;

use Illuminate\Database\Eloquent\Model;

class sr_pengembalian_model extends Model
{
    protected $table    = 'sr_pengembalian';
    protected $id       = ['No_Pengembalian'];
    protected $fillable = [
                          'No_Pengembalian',
                          'ID_Spreading',
                          'Tanggal_Pelaporan',
                          'Kode_Rayon',
                          'Kode_Gudang',
                          'Posted',
                          'Time_Stamp',
                          'User_ID'                        
                          ];
    public $timestamps = false;
}
