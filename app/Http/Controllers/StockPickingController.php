<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\in_delivery_model;
use App\in_delivery_detail_model;
use App\in_delivery_subdetail_model;

use App\Purchase\pc_purchase_order_model;
use App\Purchase\pc_purchase_order_detail_model;

use App\Purchase\pc_barang_datang_harga_model;
use App\Purchase\pc_barang_datang_detail_model;
use App\Purchase\pc_barang_datang_model;

use App\StockPicking\ms_mapping_pobd_srrc_modell;
use App\mapping\ms_odoo_map_uom_product_model;

use App\Returns\sl_terima_barang_retur_model;
use App\Returns\sl_terima_barang_retur_detail_model;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
 

class StockPickingController extends Controller
{
    public function get_faktur_retur(request $request)
    {
          $partner_id  = $request->partner_id;
          $product_id  = $request->product_id;
          $lot_number  = $request->lot_number;
         
          $odoo = new \Edujugon\Laradoo\Odoo();
          $odoo = $odoo->connect();     
          
          $data_pelanggan = $odoo->where('id','=',  $partner_id)   
                                 ->where('customer','=',true)  
                                 ->fields('id',
                                          'internal_code',
                                          'name')
                                 ->limit(1)                             
                                 ->get('res.partner');

                            
          if ((count($data_pelanggan))>0) 
          {
             $kode_pelanggan = $data_pelanggan[0]['internal_code'];
          }
          else
          {
             response()->json([ 
                                'success'=>0,
                                'message'=>"Partner ID ". $partner_id.' Tidak Ada / Belum di Mapping !'                  
                              ])->send();  
             exit;
          }
           
                   
          
          $data_barang = ms_odoo_map_uom_product_model::where('product_id', $product_id)
                                                      ->select('product_code')    
                                                      ->limit(1)                                                   
                                                      ->get();
          if ((count($data_barang))>0) 
          {
            $kode_barang = $data_barang[0]['product_code'];
          }
          else
          {
            response()->json
            ([ 
                'success'=>0,
                'message'=>"Product ID ". $product_id.' Tidak Ada / Belum di Mapping !'                     
            ])->send();  
            exit;
          }
       
                

          $lot_data = $odoo->where('name','=',  $lot_number)     
                           ->where('product_id','=', $product_id )        
                           ->fields('id','name')
                           ->limit(1)                             
                           ->get('stock.production.lot');
       

          if (count($lot_data)>0) 
          {
             $lot_id = $lot_data[0]['id'];  
          }
          else
          {

           
             response()->json(
                                [ 
                                'success'=>0,
                                'message'=>'Lot Number '. $lot_number.' Tidak Ada / Belum di Mapping !'                     
                                ]
                            )->send();                
            exit;
          }
                        
          
        //  $lot_id = $lot_data[0]['id'];   
                 
       
          $data=sl_terima_barang_retur_detail_model::where('sl_terima_barang_retur_detail.Batch',$lot_number)
                                                    ->where('sl_terima_barang_retur_detail.Kode_Barang', $kode_barang)   
                                                    ->where('sl_terima_barang_retur.Kode_Pelanggan',$kode_pelanggan)
                                                    ->join('sl_terima_barang_retur', 'sl_terima_barang_retur.No_TBR', '=', 'sl_terima_barang_retur_detail.No_TBR')                                        
                                                    ->leftjoin(DB::raw('(select 
                                                              momu2.uom_id,
                                                              sfd.No_Faktur,
                                                              sfd.Kode_Barang,
                                                              sfd.Jumlah  
                                                              FROM 
                                                              `sl_faktur_detail` sfd inner join
                                                              `ms_odoo_map_uom_product` momu2
                                                              on momu2.product_code=sfd.Kode_Barang
                                                              AND momu2.uom_long_name=sfd.Satuan
                                                              ) fkt_detail ') ,         
                                                                 function($join)
                                                                 {
                                                                   $join->on('fkt_detail.No_Faktur','=','sl_terima_barang_retur_detail.No_Faktur');
                                                                   $join->on('fkt_detail.Kode_Barang','=','sl_terima_barang_retur_detail.Kode_Barang');
                                                                 }
                                                        )       
                                                     ->select('sl_terima_barang_retur_detail.No_Faktur',
                                                              'fkt_detail.Jumlah as Max_Jumlah',
                                                              'fkt_detail.uom_id as Max_Uom_ID')
                                                     ->get();

        
        if (count($data)>0)
        {
            response()->json([ 
                'success'=>1,
                'data'=>$data                    
            ])->send();   
         }  else
         {
            response()->json([ 
                'success'=>0,
                'message'=>"Data Tidak ditemukan"                    
            ])->send();      
         }   
                                                   

    }



    public function get_detail_po(request $request)
    {
          $no_po  = $request->no_po;
          #return $no_po;          
       
          $data=pc_purchase_order_detail_model::where('pc_purchase_order_detail.No_PO', $no_po)                                                    
                                                ->join('ms_odoo_map_uom_product',  function($join)
                                                {
                                                  $join->on('pc_purchase_order_detail.Kode_Barang','=','ms_odoo_map_uom_product.product_code');
                                                  $join->on('pc_purchase_order_detail.Satuan','=','ms_odoo_map_uom_product.uom_long_name');
                                                })
                                                ->select('ms_odoo_map_uom_product.product_id',
                                                         'ms_odoo_map_uom_product.uom_id',
                                                         'pc_purchase_order_detail.Jumlah as uom_qty')
                                                ->get();
           
          $data_item_po = [];
          $item_data    = [];
          $i            = 0;
          foreach ($data as $item_data[])
          {
            $data_item_po[$i]['product_id'] = $item_data[$i]['product_id'];   
            $data_item_po[$i]['uom_id']     = $item_data[$i]['uom_id'];   
            $data_item_po[$i]['uom_qty']    = $item_data[$i]['uom_qty'];   
            $i++;                                        
          }

         #return($data_item_po);
        
        if (count($data)>0)
        {
            response()->json([ 
                'success'=>1,
                'no_po'=>$no_po,
                'item_po'=>$data_item_po                   
            ])->send();   
         }  else
         {
            response()->json([ 
                'success'=>0,
                'message'=>'Item PO : '.$no_po.' Tidak ditemukan !'                    
            ])->send();      
         }   
                                                   

    }


    public function act_afterconfirm_stockrequest(request $request)
    {
          $stock_request_id  =  $request->stock_request_id;
          $stock_picking_id  =  $request->stock_picking_id;
          $no_referensi      =  $request->no_referensi;


          $odoo = new \Edujugon\Laradoo\Odoo();
          $odoo = $odoo->connect();     
          
          $picking_header = $odoo->where('id','=', $stock_picking_id)                             
                                 ->fields('id',
                                          'name',
                                          'scheduled_date',
                                          'state')
                                 ->limit(1)                             
                                 ->get('stock.picking');

          $stock_request = $odoo->where('id','=', $stock_picking_id)                             
                                ->fields('id',
                                         'name',
                                         'state'
                                         )
                                ->limit(1)                             
                                ->get('stock.request.order');

          $stock_request_state  = $stock_request[0]['state'];
          
          if(count($picking_header)<=0) {
            
              response()->json([ 
                  'success'=>0,
                  'message'=>'Picking ID '.$stock_picking_id.' Belum terbentuk !'
              ])->send();   
              exit;
          } else
          {           
            $picking_date   = $picking_header[0]['scheduled_date'];
            $picking_status = $picking_header[0]['state'];  
            $picking_name   = $picking_header[0]['name'];              
          }
         

          $save_data      = ms_mapping_pobd_srrc_modell::where('request_id',$stock_request_id)
                                                       ->where('no_bd',$no_referensi) 
                                                       ->update([
                                                                'picking_id'=>$stock_picking_id,
                                                                'picking_date'=>$picking_date,
                                                                'picking_status'=>$picking_status,
                                                                'picking_number'=>$picking_name,
                                                                'request_status'=>$stock_request_state
                                                        ]);   
                                                       //->where('picking_id',$stock_picking_id) 
                                                       //->select ('request_id')
                                                       ///->get();  
         // return 'bd'.$no_referensi.'  picking id '.$stock_picking_id.'  Stock Request ID  '.$stock_request_id;
                                                       /* 
                                                      
                                                       */      
         if ($save_data){
            response()->json([ 
                'success'=>1                    
            ])->send();   
         }  else
         {
            response()->json([ 
                'success'=>0                    
            ])->send();      
         }    
    }

    public function getmacpicking_item($picking_id,$direction)
    {        
        if($direction=='inbound') { //barang_datang
            $data_row = ms_mapping_pobd_srrc_modell::where('picking_id',$picking_id)                    
                                                    ->join('pc_barang_datang','pc_barang_datang.No_BD','=','ms_mapping_pobd_srrc.no_bd')                                                                                                 
                                                    ->join('pc_barang_datang_detail', 'pc_barang_datang_detail.No_BD','=','pc_barang_datang.No_BD')                                                         
                                                    ->join('ms_odoo_map_uom_product', function ($join) 
                                                        {
                                                          $join->on('ms_odoo_map_uom_product.product_code', '=', 'pc_barang_datang_detail.Kode_Barang');
                                                          $join->on('ms_odoo_map_uom_product.uom_long_name', '=', 'pc_barang_datang_detail.Satuan');                                                          
                                                        }) 
                                                    ->select(
                                                            'ms_mapping_pobd_srrc.picking_id',
                                                            'ms_mapping_pobd_srrc.picking_date',
                                                            'ms_mapping_pobd_srrc.picking_status',                                                        
                                                            'ms_odoo_map_uom_product.product_id',
                                                            'ms_odoo_map_uom_product.uom_id',
                                                            'pc_barang_datang.No_BD as NO_BD',
                                                            'pc_barang_datang.Tgl_BD as Tgl_BD',
                                                            'pc_barang_datang_detail.Kode_Barang',
                                                            'pc_barang_datang_detail.Jumlah',
                                                            'pc_barang_datang_detail.Satuan',                                                        
                                                            'pc_barang_datang_detail.No_Batch',
                                                            'pc_barang_datang_detail.Kadaluarsa'
                                                    )                                                                                                                                                 
                                                    ->get();  
          
    }
    else  if($direction=='outbound') { //do
            $data_row = ms_mapping_pobd_srrc_modell::where('picking_id',$picking_id)                    
                                                    ->join('in_delivery','in_delivery.No_Delivery','=','ms_mapping_pobd_srrc.no_bd')                                                                                                 
                                                    ->join('in_delivery_subdetail', 'in_delivery_subdetail.No_Delivery','=','in_delivery.No_Delivery')                                                         
                                                    ->join('ms_odoo_map_uom_product', function ($join) 
                                                        {
                                                        $join->on('ms_odoo_map_uom_product.product_code', '=', 'in_delivery_subdetail.Kode_Barang');
                                                        $join->on('ms_odoo_map_uom_product.uom_long_name', '=', 'in_delivery_subdetail.Satuan');                                                          
                                                        }) 
                                                    ->select(
                                                            'ms_mapping_pobd_srrc.picking_id',
                                                            'ms_mapping_pobd_srrc.picking_date',
                                                            'ms_mapping_pobd_srrc.picking_status',                                                        
                                                            'ms_odoo_map_uom_product.product_id',
                                                            'ms_odoo_map_uom_product.uom_id',
                                                            'in_delivery.No_Delivery as NO_BD',
                                                            'in_delivery.Tgl_Delivery as Tgl_BD',
                                                            'in_delivery_subdetail.Kode_Barang',
                                                            'in_delivery_subdetail.Jumlah',
                                                            'in_delivery_subdetail.Satuan',                                                        
                                                            'in_delivery_subdetail.No_Batch',
                                                            'in_delivery_subdetail.Kadaluarsa'
                                                    )                                                                                                                                                 
                                                    ->get();                  

    }

        $rowCount    = 0;
        $data_result = [];

        foreach ($data_row as  $data_item)
        {
            $data_result[$rowCount]['picking_id']    =  $data_item['picking_id'];
            $data_result[$rowCount]['product_id']    =  $data_item['product_id'];
            $data_result[$rowCount]['uom_id']        =  $data_item['uom_id'];            
            $data_result[$rowCount]['uom_qty']       =  $data_item['Jumlah'];        
            $data_result[$rowCount]['lot_number']    =  $data_item['No_Batch'];                    
            $data_result[$rowCount]['expired_date']  =  $data_item['Kadaluarsa'];                    

            $rowCount++;
        }  
         
        return $data_result;
        //return $data_result;
        if (count($data_result)>0) 
        {   
            response()->json([ 
                             'success'=>1,                       
                             'data'=>$data_result                                      
                             ])->send();   
        }
        else 
        {
            response()->json([ 
                              'success'=>0,                    
                              'message'=>'Item untuk Picking ID '.$picking_id.' Belum di ditemukan di MAC !'                  
                             ])->send();  
        } 
     
    }

}