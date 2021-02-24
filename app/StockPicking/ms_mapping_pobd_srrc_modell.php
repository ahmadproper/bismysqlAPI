<?php

namespace App\StockPicking;

use Illuminate\Database\Eloquent\Model;

class ms_mapping_pobd_srrc_modell extends Model
{
    protected $table='ms_mapping_pobd_srrc';
    protected $id='id';
    protected $primaryKey = 'id';
    protected $fillable =[                 
                            'request_id',  
                            'request_number',       
                            'request_date',  
                            'request_status',  
                            'picking_id',  
                            'picking_number',      
                            'picking_date',  
                            'picking_status',
                            'no_po',                        
                            'tgl_po',      
                            'po_status',  
                            'no_bd',               
                            'tgl_bd',      
                            'status_bd'  
                        ];
    public $timestamps = false;
}
