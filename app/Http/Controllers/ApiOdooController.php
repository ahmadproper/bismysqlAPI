<?php
namespace App\Http\Controllers;
use App\in_delivery_model;
use App\in_delivery_detail_model;
use App\in_delivery_subdetail_model;

use App\ms_mapping_uom_odoo_model;
use App\ms_mapping_product_odoo_model;
use App\ms_mapping_satuan_map_model;
use App\ms_mapping_spg_odoo_model;


class ApiOdooController extends Controller
{    
    public function getPickingItem($picking_id)
    {        
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect(); 
        
        $no_delivery = $odoo->where('id','=',$picking_id)
                            ->where('state','=','done')
                            ->where('picking_type_id','=',2)
                            ->fields('origin')
                            ->limit(1)                             
                            ->get('stock.picking');          
        

        $stock_move_line = $odoo->where('picking_id.id','=',$picking_id)   
                                ->get('stock.move.line');

 
            $rowCount = 0;
            foreach ($stock_move_line as  $details)
            {
                $Do_Detail[$rowCount]['No_Delivery']       = $no_delivery[0]['origin'];
                $Do_Detail[$rowCount]['Kode_Gudang']       = 'GDG01';                  
                
                $product = $odoo->where('id','=',$details['product_id'][0])                             
                                ->fields('default_code','barcode')
                                ->limit(1)                             
                                ->get('product.product');                                      

                $batch = $odoo->where('id','=',$details['lot_id'][0])                             
                              ->fields('name','expiry_date')
                              ->limit(1)                             
                              ->get('stock.production.lot');   

                $Do_Detail[$rowCount]['Kode_Barang']        = $product[0]['default_code'];
                $Do_Detail[$rowCount]['No_Batch']           = $batch[0]['name'];
                $Do_Detail[$rowCount]['Jumlah']             = $details['qty_done'];
                $Do_Detail[$rowCount]['Satuan']             = $details['product_uom_id'][1];
                $Do_Detail[$rowCount]['Kadaluarsa']         = $batch[0]['expiry_date'];
                $Do_Detail[$rowCount]['Terima']             = $details['qty_done'];
                $Do_Detail[$rowCount]['ID_Program_Promosi'] = '';
                $rowCount++;   
            }         

         
    }

    public function get_moveid($picking_id, $product_id, $product_uom)//, $product_id)
    {       
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();//$request->get('email');      

        $ids = $odoo->where('picking_id.id', $picking_id)
                    ->where('product_id.id', $product_id)
                    ->where('product_uom.id', $product_uom)
                    ->search('stock.move');       
        
        return $ids;
    }


    public function get_rqmoveid(Request $request )
    {
        //$product_uom= $request->prodcut_uom; 
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect(); 

        $picking_id  = $request->picking_id;
        $product_id  = $request->product_id;
        $product_uom = $request->product_uom;

        $result1     = $odoo->where('picking_id.id','=',$picking_id)                            
                            ->where('product_id.id','=',$product_id)
                            ->where('product_uom.id','=',$product_uom)
                            ->search('stock.move');   
        return $result1;         
    }

    public function get_rqStockMoveLine(Request $request )
    {
        //$product_uom= $request->prodcut_uom; 
        $odoo         = new \Edujugon\Laradoo\Odoo();
        $odoo         = $odoo->connect(); 
        $no_delivery  = $request->no_delivery;
        $no_delivery1 = substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5); 

        //$product_id = $request->product_id;
        //$product_uom = $request->product_uom;

        
        $stock_move_line = $odoo->where('picking_id.origin','=',$no_delivery1)   
                                ->where('picking_id.state','=','done')                               
                                ->search('stock.move.line');   
                        

        return $stock_move_line;         
    }
    
    
    public function getstock_prod_lot_id($product_id,$product_uom_id,$no_batch,$kode_barang)
    {
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();
        $lot_id = $odoo ->where('product_id.id', $product_id)
                        ->where('product_uom_id.id', $product_uom_id)
                        ->where('name', $no_batch )   
                        ->where('ref', $kode_barang)                 
                        ->search('stock.production.lot');   
        return($lot_id);         
    }


    public function confirmStockPicking($picking_id)
    {
        $odoo   = new \Edujugon\Laradoo\Odoo();
        $odoo   = $odoo->connect();   
        $result = $odoo->call(
                            'stock.picking', 
                            'action_confirm',
                            [(int)$picking_id]             
                           );
        return($result);             
    }


    public function create_spg($no_delivery)
    {
        $no_delivery1= substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5); 
        if (ms_mapping_spg_odoo_model::where('no_delivery', $no_delivery1)->exists())
        {
            $hasil='NO SPG :'.$no_delivery1.' Sudah Pernah di Buat !';
        }
        else
        { 
            if (in_delivery_model::where('no_delivery', $no_delivery1)->exists())
            { 
                    $odoo = new \Edujugon\Laradoo\Odoo();
                    $odoo = $odoo->connect();           

                   
                    $header = in_delivery_model::where('no_delivery',$no_delivery1)->get(); 
                    $detail = in_delivery_detail_model::where('no_delivery',$no_delivery1)        
                                                        ->leftjoin('ms_satuan_map','in_delivery_detail.satuan','=','ms_satuan_map.satuanBSP')    
                                                        ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                                    {
                                                                    $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                                    $join->on('in_delivery_detail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                                    }                       
                                                                )
                                                        ->leftjoin('ms_mapping_product_odoo','in_delivery_detail.kode_barang','=','ms_mapping_product_odoo.kode_barang')       
                                                        ->select(
                                                                'in_delivery_detail.*',
                                                                'ms_satuan_map.satuanSAP as satuan_odoo',
                                                                'ms_mapping_uom_odoo.uom_id',
                                                                'ms_mapping_product_odoo.product_id'                           
                                                                )
                                                        ->get();  

                    /* 
                    $subdetail= in_delivery_subdetail_model::where('no_delivery',$no_delivery1)        
                                                            ->leftjoin('ms_satuan_map','in_delivery_subdetail.satuan','=','ms_satuan_map.satuanBSP')    
                                                            ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                                        {
                                                                            $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                                            $join->on('in_delivery_subdetail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                                        }                       
                                                                    )
                                                            ->leftjoin('ms_mapping_product_odoo','in_delivery_subdetail.kode_barang','=','ms_mapping_product_odoo.kode_barang')        
                                                            ->select('in_delivery_subdetail.*',
                                                                    'ms_satuan_map.satuanSAP as satuan_odoo',
                                                                    'ms_mapping_uom_odoo.uom_id',
                                                                    'ms_mapping_product_odoo.product_id'                           
                                                                    )
                                                            ->get(); */


                    //create Stock.picking Model 
                    $Do_Header['No_Delivery']  = $header[0]['No_Delivery'];
                    $Do_Header['Tgl_Delivery'] = $header[0]['Tgl_Delivery'];
                    $stock_picking_id = $odoo->create('stock.picking',[
                                                      'origin' => $Do_Header['No_Delivery'],
                                                      'location_id'=>20,
                                                      'location_dest_id'=>1,
                                                      'picking_type_id'=>7,//delivery Order
                                                      'delivery_date'=> $Do_Header['Tgl_Delivery']]
                                                     );       
                
                
                    //Stock.Move Model       
                    $rowCount = 0 ;
                    foreach ($detail as  $details)
                    {                                     
                        $Do_Detail[$rowCount]['No_Delivery'] = $details['No_Delivery'];
                        $Do_Detail[$rowCount]['Kode_Barang'] = $details['Kode_Barang'];
                        $Do_Detail[$rowCount]['Jumlah']      = $details['Jumlah'];       
                        $Do_Detail[$rowCount]['uom_id']      = $details['uom_id'];
                        $Do_Detail[$rowCount]['product_id']  = $details['product_id']; 
                        
                        $stock_move_id = $odoo->create('stock.move',
                                                        [    
                                                       'name' => 'DO:'.$Do_Detail[$rowCount]['No_Delivery'],                     
                                                       'product_id' => $Do_Detail[$rowCount]['product_id'],
                                                       'product_uom_qty' =>  $Do_Detail[$rowCount]['Jumlah'],
                                                       'quantity_done' =>  $Do_Detail[$rowCount]['Jumlah'],
                                                       'reservered_availability' =>  $Do_Detail[$rowCount]['Jumlah'],
                                                       'product_uom' => $Do_Detail[$rowCount]['uom_id'],
                                                       'picking_id' => $stock_picking_id,
                                                       'location_id' => 20,
                                                       'location_dest_id' => 1
                                                        ]); 

                        /*                             
                        $batch = in_delivery_subdetail_model::where('no_delivery',$no_delivery1)   
                                                            ->where('in_delivery_subdetail.kode_barang',$Do_Detail[$rowCount]['Kode_Barang'])      
                                                            ->leftjoin('ms_satuan_map','in_delivery_subdetail.satuan','=','ms_satuan_map.satuanBSP')    
                                                            ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                                        {
                                                                            $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                                            $join->on('in_delivery_subdetail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                                        }                       
                                                                    )
                                                            ->leftjoin('ms_mapping_product_odoo','in_delivery_subdetail.kode_barang','=','ms_mapping_product_odoo.kode_barang')        
                                                            ->select('in_delivery_subdetail.*',
                                                                    'ms_satuan_map.satuanSAP as satuan_odoo',
                                                                    'ms_mapping_uom_odoo.uom_id',
                                                                    'ms_mapping_product_odoo.product_id'                           
                                                                    )
                                                            ->get();


                        $rowCount1=0;
                        foreach ($batch as  $batch_detail)                    
                        {
                            $Do_SubDetail[$rowCount1]['No_Delivery']=$batch_detail['No_Delivery'];
                            $Do_SubDetail[$rowCount1]['Kode_Barang']=$batch_detail['Kode_Barang'];
                            $Do_SubDetail[$rowCount1]['Jumlah']     =$batch_detail['Jumlah'];       
                            $Do_SubDetail[$rowCount1]['uom_id']     =$batch_detail['uom_id'];
                            $Do_SubDetail[$rowCount1]['product_id'] =$batch_detail['product_id']; 
                            $Do_SubDetail[$rowCount1]['no_batch']   =$batch_detail['No_Batch']; 
                            $Do_SubDetail[$rowCount1]['move_id']    =$stock_move_id; 
                            
                            $lot_id = $odoo ->where('product_id.id', $Do_SubDetail[$rowCount1]['product_id'])
                                            ->where('product_uom_id.id', $Do_SubDetail[$rowCount1]['uom_id'])
                                            ->where('name', $Do_SubDetail[$rowCount1]['no_batch'])   
                                            ->where('ref', $Do_SubDetail[$rowCount1]['Kode_Barang'])                 
                                            ->search('stock.production.lot');   
           
    
                            $stock_move_line = $odoo->create('stock.move.line',[                                 
                                                             'move_id' =>  $Do_SubDetail[$rowCount1]['move_id'],
                                                             'lot_id'=> $lot_id,  
                                                             'lot_name'=> $Do_SubDetail[$rowCount1]['no_batch'],                                                             
                                                             'product_id' => $Do_SubDetail[$rowCount1]['product_id'],
                                                             'product_uom_qty' =>  $Do_SubDetail[$rowCount1]['Jumlah'],
                                                             'product_qty' =>  $Do_SubDetail[$rowCount1]['Jumlah'],
                                                             'product_uom_id' =>  $Do_SubDetail[$rowCount1]['uom_id'],
                                                             'qty_done' =>  $Do_SubDetail[$rowCount1]['Jumlah']                                                            
                                                             ]                     
                                                        );                    
                     
                             $rowCount1++;
                        }   

                         */
                    
                        $rowCount++;       
                    } 



                    //Action_Confirm Stock Picking Method
                    $result=$odoo->call(
                                   'stock.picking', 
                                   'action_confirm',
                                   [(int)$stock_picking_id]             
                                );                         
                                

                       /*==== Begin Stock Move Line   */
                           
                            /*        
                            $batch = in_delivery_subdetail_model::where('no_delivery',$no_delivery1)   

                                                                ->leftjoin('ms_satuan_map','in_delivery_subdetail.satuan','=','ms_satuan_map.satuanBSP')    
                                                                ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                                            {
                                                                                $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                                                $join->on('in_delivery_subdetail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                                            }                       
                                                                        )
                                                                ->leftjoin('ms_mapping_product_odoo','in_delivery_subdetail.kode_barang','=','ms_mapping_product_odoo.kode_barang')        
                                                                ->select('in_delivery_subdetail.*',
                                                                        'ms_satuan_map.satuanSAP as satuan_odoo',
                                                                        'ms_mapping_uom_odoo.uom_id',
                                                                        'ms_mapping_product_odoo.product_id'                           
                                                                        )
                                                                ->get(); */

                            $rowCount1=0;

                            foreach ($batch as  $batch_detail)                    
                            {
                              
                                $Do_SubDetail[$rowCount1]['No_Delivery']= $batch_detail['No_Delivery'];
                                $Do_SubDetail[$rowCount1]['Kode_Barang']= $batch_detail['Kode_Barang'];
                                $Do_SubDetail[$rowCount1]['Jumlah']     = $batch_detail['Jumlah'];       
                                $Do_SubDetail[$rowCount1]['uom_id']     = $batch_detail['uom_id'];
                                $Do_SubDetail[$rowCount1]['product_id'] = $batch_detail['product_id']; 
                                $Do_SubDetail[$rowCount1]['no_batch']   = $batch_detail['No_Batch'];

                                 
                                $ids = $odoo->where('picking_id.id', $stock_picking_id)
                                            ->where('product_id.id', $Do_SubDetail[$rowCount1]['product_id'])
                                            ->where('product_uom.id', $Do_SubDetail[$rowCount1]['uom_id'])
                                            ->search('stock.move');       
                                            
                  
                                $Do_SubDetail[$rowCount1]['move_id'] = $ids; 
                                
                                $lot_id = $odoo ->where('product_id.id', $Do_SubDetail[$rowCount1]['product_id'])
                                                ->where('product_uom_id.id', $Do_SubDetail[$rowCount1]['uom_id'])
                                                ->where('name', $Do_SubDetail[$rowCount1]['no_batch'])   
                                                ->where('ref', $Do_SubDetail[$rowCount1]['Kode_Barang'])                 
                                                ->search('stock.production.lot'); 
                              //  $intLotid = (int)$lot_id  ;
                
        
                                $stock_move_line = $odoo->create('stock.move.line',[                                 
                                                                 'move_id' =>  $Do_SubDetail[$rowCount1]['move_id'],
                                                                 'lot_id'=> $lot_id,                                                         
                                                                 'product_id' => $Do_SubDetail[$rowCount1]['product_id'],
                                                                 'product_uom_qty' =>  $Do_SubDetail[$rowCount1]['Jumlah'],
                                                                 'product_qty' =>  $Do_SubDetail[$rowCount1]['Jumlah'],
                                                                 'product_uom_id' =>  $Do_SubDetail[$rowCount1]['uom_id'],
                                                                 'qty_done' =>  $Do_SubDetail[$rowCount1]['Jumlah']                                                            
                                                                    ]                     
                                                            );                    
                            
                                $rowCount1++;
                            } 
                
                       
                      

                       /* ==== end Stock Move Line   */
                                       
                    //insert to ms_mapping_spg_odoo model (mysql)
                    $ms_mapping_spg_odoo              = new ms_mapping_spg_odoo_model;
                    $ms_mapping_spg_odoo->no_delivery = $no_delivery1;
                    $ms_mapping_spg_odoo->picking_id  = $stock_picking_id;
                    $ms_mapping_spg_odoo->save();                                

                    
                    $hasil='No SPG [Delivery '.$no_delivery.'] [Picking ID : '.$stock_picking_id.' ]   Lot_ID : '.$lot_id.'   Berhasil di Confirm ..';

               } 
               else
              {
               $hasil='NO SPG :'.$no_delivery1.' Tidak Ada di BISMySQL !'; 
              }              
        }
         return response($hasil);           
    }


    public function getDelivery($no_delivery)  
    {   //doblg20200100001
        //DOBLG/202002/09864
        $no_delivery1 = substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5); 
        $header = in_delivery_model::where('no_delivery',$no_delivery1)->get();
        $detail = in_delivery_detail_model::where('no_delivery',$no_delivery1)        
                                            ->leftjoin('ms_satuan_map','in_delivery_detail.satuan','=','ms_satuan_map.satuanBSP')    
                                            ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                        {
                                                        $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                        $join->on('in_delivery_detail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                        }                       
                                                    )
                                            ->leftjoin('ms_mapping_product_odoo','in_delivery_detail.kode_barang','=','ms_mapping_product_odoo.kode_barang')       
                                            ->select(
                                                    'in_delivery_detail.*',
                                                    'ms_satuan_map.satuanSAP as satuan_odoo',
                                                    'ms_mapping_uom_odoo.uom_id',
                                                    'ms_mapping_product_odoo.product_id'                           
                                                    )
                                            ->get();

        $subdetail= in_delivery_subdetail_model::where('no_delivery',$no_delivery1)        
                                                ->leftjoin('ms_satuan_map','in_delivery_subdetail.satuan','=','ms_satuan_map.satuanBSP')    
                                                ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                            {
                                                                $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                                $join->on('in_delivery_subdetail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                            }                       
                                                    )
                                                ->leftjoin('ms_mapping_product_odoo','in_delivery_subdetail.kode_barang','=','ms_mapping_product_odoo.kode_barang')        
                                                ->select('in_delivery_subdetail.*',
                                                            'ms_satuan_map.satuanSAP as satuan_odoo',
                                                            'ms_mapping_uom_odoo.uom_id',
                                                            'ms_mapping_product_odoo.product_id'                           
                                                        )
                                                ->get();

        $result['in_delivery']           = $header;
        $result['in_delivery_detail']    = $detail;//tambahin uom_id & product_id
        $result['in_delivery_subdetail'] = $subdetail;//tambahin uom_id & product_id        
        return $result;
    }



    public function getDelivery2($no_delivery)  
    {   //doblg20200100001
        //DOBLG/202002/09864
        $no_delivery1= substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5); 
        $header= in_delivery_model::where('no_delivery',$no_delivery1)->get();
        $detail= in_delivery_detail_model::where('no_delivery',$no_delivery1)        
                                            ->leftjoin('ms_satuan_map','in_delivery_detail.satuan','=','ms_satuan_map.satuanBSP')    
                                            ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                        {
                                                        $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                        $join->on('in_delivery_detail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                        }                       
                                                    )
                                            ->leftjoin('ms_mapping_product_odoo','in_delivery_detail.kode_barang','=','ms_mapping_product_odoo.kode_barang')       
                                            ->select(
                                                    'in_delivery_detail.*',
                                                    'ms_satuan_map.satuanSAP',
                                                    'ms_mapping_uom_odoo.uom_id',
                                                    'ms_mapping_product_odoo.product_id'                           
                                                    )
                                            ->get();

        $subdetail = in_delivery_subdetail_model::where('no_delivery',$no_delivery1)        
                                                  ->leftjoin('ms_satuan_map','in_delivery_subdetail.satuan','=','ms_satuan_map.satuanBSP')    
                                                  ->leftjoin('ms_mapping_uom_odoo', function($join)
                                                        {
                                                                $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                                                                $join->on('in_delivery_subdetail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                                                        }                       
                                                        )
                                                  ->leftjoin('ms_mapping_product_odoo','in_delivery_subdetail.kode_barang','=','ms_mapping_product_odoo.kode_barang')        
                                                  ->select('in_delivery_subdetail.*',
                                                            'ms_satuan_map.satuanSAP',
                                                            'ms_mapping_uom_odoo.uom_id',
                                                            'ms_mapping_product_odoo.product_id'                           
                                                        )
                                                 ->get();

        $result['in_delivery']           = $header;
        $result['in_delivery_detail']    = $detail;//tambahin uom_id & product_id
        $result['in_delivery_subdetail'] = $subdetail;//tambahin uom_id & product_id        
        return $result;
    }


 
 
    public function sendDOToOdoo($no_delivery)  
    {   //doblg20200100001
        //DOBLG/202002/09864

        $product = $odoo->fields('id','default_code')
                        ->get('product_product');

        $no_delivery1= substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5); 
        $header= in_delivery_model::where('no_delivery',$no_delivery1)->get();  
        $detail= in_delivery_detail_model::where('no_delivery',$no_delivery1)        
               ->leftjoin('ms_satuan_map','in_delivery_detail.satuan','=','ms_satuan_map.satuanBSP')    
               ->leftjoin('ms_mapping_uom_odoo', function($join)
                          {
                           $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                           $join->on('in_delivery_detail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                          }                       
                       )
              ->leftjoin('ms_mapping_product_odoo','in_delivery_detail.kode_barang','=','ms_mapping_product_odoo.kode_barang')       
              ->select(
                      'in_delivery_detail.*',
                      'ms_satuan_map.satuanSAP',
                      'ms_mapping_uom_odoo.uom_id',
                      'ms_mapping_product_odoo.product_id'                           
                      )
              ->first();
          
        
                /*
        $subdetail= in_delivery_subdetail_model::where('no_delivery',$no_delivery1)        
              ->leftjoin('ms_satuan_map','in_delivery_subdetail.satuan','=','ms_satuan_map.satuanBSP')    
              ->leftjoin('ms_mapping_uom_odoo', function($join)
                      {
                            $join->on('ms_satuan_map.satuanSAP','=','ms_mapping_uom_odoo.satuan_odoo');
                            $join->on('in_delivery_subdetail.kode_barang','=','ms_mapping_uom_odoo.kode_barang');                      
                       }                       
                      )
               ->leftjoin('ms_mapping_product_odoo','in_delivery_subdetail.kode_barang','=','ms_mapping_product_odoo.kode_barang')        
               ->select('in_delivery_subdetail.*',
                        'ms_satuan_map.satuanSAP',
                        'ms_mapping_uom_odoo.uom_id',
                        'ms_mapping_product_odoo.product_id'                           
                    )
               ->get();*/

       # $result['in_delivery']=$header;
        #$result['in_delivery_detail']=$detail;//tambahin uom_id & product_id
        //$result['in_delivery_subdetail']=$subdetail;//tambahin uom_id & product_id     
        $result1=$result;   
        return $result1;
    }

    public function getUomID($uom_name, $uom_category_name)    
    { 
           $odoo = new \Edujugon\Laradoo\Odoo();
           $odoo = $odoo->connect();
           $Result = $odoo->where('name','=',$uom_name) //satuanSAP                                
                          ->where('category_id.name','=',$uom_category_name) //kode_barang 
                          ->fields('id','name','category_id','product_id')
                          ->get('uom.uom');             

           return response('{"result":'.$Result.'}', 200);        
    }

    public function getProductID($product_code)    
    { 
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();
        $data = $odoo->where('default_code','=',$product_code)                                                     
                     ->fields('id')
                     ->get('product.product');             
        //return response('{"result":'.$Result!default_code.'}', 200);
       //return response('{"result":'.$Result.'}', 200);
        $Result = '{"result":'.$data.'}';
        #$Result1 = json_decode($Result, true);

        return  $Result;        
        #$Result1= json_decode($Result, true);
        //echo $yummy['toppings'][2]['type']; 
       // return $data['id'][0]; 
    }

    public function getListProduct()    
    { 
           $odoo = new \Edujugon\Laradoo\Odoo();
           $odoo = $odoo->connect();
           $Result = $odoo->fields('id','default_code','name')
                          ->limit(10)
                          ->get('product.product');             
           //return response('{"result":'.$Result.'}', 200);        
           return response($Result, 200);        
    }
  

    public function home()
    {   
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect(); 
        $purchases = $odoo->fields('id','name','date_order')
                          ->limit(10)
                          ->get('purchase.order');   
        
        return view('odoo.index',compact('purchases'));

     
    }
} 