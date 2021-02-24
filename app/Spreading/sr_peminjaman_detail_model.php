<?php

namespace App\Spreading;

use Illuminate\Database\Eloquent\Model;

class sr_peminjaman_detail_model extends Model
{
    protected $table='sr_peminjaman_detail';
    #protected $id=['No_Peminjaman'];
    protected $fillable =[
                        'No_Peminjaman',
                        'No_Detail',
                        'Kode_Barang',
                        'Satuan',
                        'Jumlah'
                        ];
    public $timestamps = false;
}
