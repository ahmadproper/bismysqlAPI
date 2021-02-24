<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sy_konfigurasi_model extends Model
{
    protected $table = 'sy_konfigurasi';     
    protected $id='item';   
    protected $fillable=['Item','Nilai']; 
    public $timestamps=False;   
}
