<?php

namespace App\Spreading;

use Illuminate\Database\Eloquent\Model;

class sr_peminjaman_model extends Model
{
    protected $table    ='sr_peminjaman';
    protected $id       =['No_Peminjaman'];
    protected $fillable =[
                        'No_Peminjaman',
                        'ID_Spreading',
                        'Tanggal_Pinjam',
                        'Kode_Rayon',
                        'Status_Tercetak',
                        'Time_Stamp',
                        'User_ID',
                        'No_Depo'        
                        ];
    public $timestamps = false;
}
