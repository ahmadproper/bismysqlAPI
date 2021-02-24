<?php

namespace App\Spreading;

use Illuminate\Database\Eloquent\Model;

class sr_pemfakturan_salesman_model extends Model
{
    protected $table    = 'sr_pemfakturan_salesman';
    protected $id       = ['no_pemfakturan'];                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
    protected $fillable = [
                          'no_pemfakturan',      
                          'kode_salesman'      
                          ];           
    public $timestamps = false;
}
