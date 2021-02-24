<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\pc_barang_datang_model;
use App\pc_barang_datang_detail_model;
use App\pc_barang_datang_harga_model;
use Illuminate\Http\Request;

class PurchaseReceiveController extends Controller
{
    public function getReceiving($no_bpb, $no_do)     
    {              
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect(); 

        $result = $odoo->call(
                            'stock.picking', 
                            'get_summarize_move_line',                                                                               
                             [0,$no_bpb,$no_do]
                             );
        return($result);               
    }  


    public function getListReceiving()     
    {              
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect(); 

        $result = $odoo->call(
                            'stock.picking', 
                            'get_picking_list',                                                                               
                             [0,'2020-09-01','2020-09-30','KUI','KUI']
                           );
        return($result);               
    }  
}
