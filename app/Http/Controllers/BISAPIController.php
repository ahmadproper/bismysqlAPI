<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
 
use App\in_delivery_model;
use App\in_delivery_detail_model;
use App\in_delivery_subdetail_model;
use App\sl_surat_pesanan_model;
use App\sl_surat_pesanan_detail_model;
use App\mapping\ms_odoo_map_uom_product_model;
use App\mapping\in_delivery_flag_wms_model;
use App\ms_barang_satuan_model;
use App\in_stock_opname_model;
use App\in_stock_opname_selisih_model;
use App\in_kartu_stok_detail_model;
use App\in_delivery_subdetail_history_model;
use Session; 

use Illuminate\Http\Request;

class BISAPIController extends Controller
{
  
   // public function getFromAPI(request $request)
   // {
      //  $no_delivery = $request->no_delivery;
        /*
        $no_delivery = $request->no_delivery;
        $urlAPI='http://192.168.21.175/api/bismysql/getDeliveryRow/'.$no_delivery;

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',  $urlAPI);
        */
      //  return response()->json(['data'=>$no_delivery]);

      //   }

    public function KartuStokAdjustment(request $request)
    {      
        // /KJBLG20200800001

        /* BISMySQL Query
      'insert into in_kartu_stok_detail '+
      'select  '+
      'date_format(so.tanggal,"%Y%m"),  '+
      'so.no_kertas_kerja,  '+
      '"ADJUSTMENT",  '+
      'so.tanggal, '+
      'iso.kode_barang,  '+
      'so.kode_gudang,  '+
      'case so.status_barang '+
      'when "BAIK" then "AVAILABLE" '+
      'when "SEMI RUSAK" then "DEFECT" '+
      'when "RUSAK" then "REJECT" '+
      'when "BERMASALAH" then "PENDING" '+
      'when "HOLD" then "HOLD" '+
      'end, '+
      'iso.no_batch,  '+
      'iso.level,  '+
      'sum(iso.jumlah),  '+
      'mbs.harga_beli,  '+
      '"LAINLAIN", "", '+
      'iso.kadaluarsa, '+
      'so.time_stamp '+	  
      'from '+
      'in_stock_opname so, '+
      'in_stock_opname_selisih iso, '+
      'ms_barang_satuan mbs '+
      'where  '+
      'so.no_kertas_kerja="'+no_ref+'" '+
      'and iso.no_kertas_kerja=so.no_kertas_kerja '+
      'and mbs.kode_barang=iso.kode_barang '+
      'and mbs.level=iso.level '+
      'group by so.no_kertas_kerja, so.kode_gudang, iso.kode_barang, iso.no_batch, so.status_barang, iso.kadaluarsa, iso.level';
        */
        
        $no_ref = substr($request->no_kertas_kerja,0,5).'/'.substr($request->no_kertas_kerja,5,6).'/'.substr($request->no_kertas_kerja,11,5);
                                    
        $opname = in_stock_opname_model::where('in_stock_opname.no_kertas_kerja',$no_ref) 
                                        ->where('in_stock_opname.Status','POST')
                                        ->join('in_stock_opname_selisih','in_stock_opname.no_kertas_kerja','=','in_stock_opname_selisih.no_kertas_kerja')
                                        ->join('ms_barang_satuan', function($join)
                                                        {
                                                            $join->on('ms_barang_satuan.kode_barang','=','in_stock_opname_selisih.kode_barang');
                                                            $join->on('ms_barang_satuan.level','=','in_stock_opname_selisih.level');
                                                        } 
                                        )                                                   
                                        ->select(
                                        DB::raw('date_format(in_stock_opname.tanggal,"%Y%m") Periode'),
                                        DB::raw('"ADJUSTMENT" Jenis_Transaksi'),
                                        'in_stock_opname.Tanggal as Tgl_Transaksi',
                                        'in_stock_opname.No_Kertas_Kerja',     
                                        'in_stock_opname.Kode_Principal',  
                                        'in_stock_opname.Kode_Divisi_Produk',  
                                        'in_stock_opname.Kode_Gudang',  
                                        DB::raw('case in_stock_opname.status_barang '.
                                        'when "BAIK" then "AVAILABLE" '.
                                        'when "SEMI RUSAK" then "DEFECT" '.
                                        'when "RUSAK" then "REJECT" '.
                                        'when "BERMASALAH" then "PENDING" '.
                                        'when "HOLD" then "HOLD" '.
                                        'end as Status_Barang'),
                                        'in_stock_opname_selisih.Kode_Barang',  
                                        'in_stock_opname_selisih.No_Batch',    
                                        'in_stock_opname_selisih.Kadaluarsa',
                                        DB::raw('sum(in_stock_opname_selisih.jumlah) as Jumlah'),        
                                        'ms_barang_satuan.Harga_Beli',                                 
                                        'in_stock_opname_selisih.Level',                                        
                                        'ms_barang_satuan.Satuan',
                                        'in_stock_opname_selisih.Referensi',
                                        DB::raw('Now() as TimeStamp')
                                        )                                 
                                        ->groupBy(
                                        DB::raw('date_format(in_stock_opname.tanggal,"%Y%m")'),
                                        'in_stock_opname.Tanggal',
                                        'Jenis_Transaksi',
                                        'in_stock_opname.No_Kertas_Kerja',     
                                        'in_stock_opname.Kode_Principal',  
                                        'in_stock_opname.Kode_Divisi_Produk',  
                                        'in_stock_opname.Kode_Gudang',  
                                        'in_stock_opname.Tanggal',   
                                        'in_stock_opname.status_barang',                                                                                    
                                        'in_stock_opname_selisih.No_Kertas_Kerja',     
                                        'in_stock_opname_selisih.Kode_Barang',  
                                        'in_stock_opname_selisih.No_Batch',     
                                        'in_stock_opname_selisih.Kadaluarsa', 
                                        'ms_barang_satuan.Harga_Beli',  
                                        'in_stock_opname_selisih.Level',
                                        'ms_barang_satuan.Satuan',
                                        'in_stock_opname_selisih.Referensi',
                                        'TimeStamp'
                                        ) 
                                        ->get();
        
        $rowCount=0;
 
        foreach ($opname as  $opnameItem)
        {
                $OpnameData[$rowCount]['Periode']            =  $opnameItem['Periode'];
                $OpnameData[$rowCount]['No_Transaksi']       =  $opnameItem['No_Kertas_Kerja'];
                $OpnameData[$rowCount]['Jenis_Transaksi']    =  $opnameItem['Jenis_Transaksi'];
                $OpnameData[$rowCount]['Tgl_Transaksi']      =  $opnameItem['Tgl_Transaksi'];    
                $OpnameData[$rowCount]['Barang']             =  $opnameItem['Kode_Barang'];        
                $OpnameData[$rowCount]['Gudang']             =  $opnameItem['Kode_Gudang'];  
                $OpnameData[$rowCount]['STATUS']             =  $opnameItem['Status_Barang'];
                $OpnameData[$rowCount]['Batch']              =  $opnameItem['No_Batch'];
                $OpnameData[$rowCount]['Level_Asal']         =  $opnameItem['Level'];
                $OpnameData[$rowCount]['Net']                =  $opnameItem['Jumlah'];
                $OpnameData[$rowCount]['Harga_Beli']         =  $opnameItem['Harga_Beli'];
                $OpnameData[$rowCount]['Keterangan']         =  'LAINLAIN';
                $OpnameData[$rowCount]['ID_Program_Promosi'] =  '';
                $OpnameData[$rowCount]['Expired']            =  $opnameItem['Kadaluarsa'];
                if($OpnameData[$rowCount]['Expired']!="0000-00-00") {
                    $OpnameData[$rowCount]['Expired']        =  $opnameItem['Kadaluarsa'];
                }
                else {
                    $OpnameData[$rowCount]['Expired']        = '1999-09-09';
                }
             
                
                $OpnameData[$rowCount]['TimeStamp']          =  $opnameItem['TimeStamp'];
                $rowCount++;
        }
        
       // return($OpnameData);
        if (count($OpnameData)>0) 
        {  
            
            $saved=in_kartu_stok_detail_model::insert($OpnameData);
            if($saved)
            {
            response()->json([ 
                        'success'=>1,                       
                        'data'=>$OpnameData,
                        'nomor'=>$no_ref                        
                        ])->send();  
            } 
            else 
            {
               
               response()->json([ 
                'success'=>0,                       
                'data'=>$OpnameData,
                'nomor'=>$no_ref                        
                ])->send();
                exit;
            }             
             
        } 
        else
        {   
            response()->json([ 
                'success'=>0,                    
                'message'=>'Selisih No KKSO '.$no_ref.' Tidak ada !'                        
                ])->send();    
        }                                            
    }

    public function getSessionID()
    {
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();    
             
        $session_id = Session::getId();
        $username   = $odoo->getUserName();
        $uid        = $odoo->getUid();
        $db         = $odoo->getdb();

        $sesi['session_id'] = $session_id;
        $sesi['user'] = $username;
        $sesi['uid']  = $uid;
        $sesi['db']   = $db;

        $no_delivery  = $odoo->Where('picking_type_id','in',[2,3])
                             ->where('state','=','assigned')
                          //->orWhere('picking_type_id','=',3)
                             ->fields('origin','name','picking_type_id')
                          //->limit(1)                             
                            ->get('stock.picking'); 

        #$odoo_session = $odoo->getUid();     

        if (count($no_delivery)>0) { 
            response()->json([ 
                            'success'=>1,                       
                            'data'=>$no_delivery,
                            'sesi'=>$sesi
                            ])->send();   
        }
        else
        {
            response()->json([          
                            'success'=>0,        
                            'sesi'=>$sesi
                            ])->send();       
        }    
                      
    }
    
    public function getStockMoveLineRow(request $request)
    {        
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();     
        
        $picking_id=$request->picking_id;
        try
        {
                $no_delivery = $odoo->where('id','=',$picking_id)
                                    ->where('state','=','assigned')
                                  //->orWhere('picking_type_id','=',2)
                                  //->orWhere('picking_type_id','=',3)
                                    ->fields('origin','name')
                                    ->limit(1)                             
                                    ->get('stock.picking'); 
                
                $c_data_delivery=count($no_delivery);       
                if($c_data_delivery<=0){        
                     //$res['success']=0;    
                     //$res['code']=400;            
                     response()->json(['success'=>0,
                                       'code'=>400,
                                       'message'=>'No Delivery berdasarkan Picking ID : '.$picking_id.' Tidak ada di BISMySQL !'])->send(); 
                     exit;   
                 }    
                
                $stock_move_line = $odoo->where('picking_id.id','=',$picking_id)   
                                        ->get('stock.move.line');

                $c_stock_move_line=count($stock_move_line);
                if($c_stock_move_line<=0){       
                   // $res['success']=0;    
                    //$res['code']=400;                 
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Tidak Ada Item di stock move line !'])->send(); 
                    exit;   
                }    
                             
               
                $no_sp = in_delivery_model::where('no_delivery',$no_delivery[0]['origin'])
                                           ->select('kode_referensi')
                                           ->get();                                     
                $c_no_sp = count($no_sp);           
                
                
                if($c_no_sp<=0){        
                    //$res['success']=0;    
                    //$res['code']=400;                  
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'No SP untuk DO '.$no_delivery.' Tidak Ada !'])->send(); 
                    exit;   
                }                                     
                                    
                                          
                if( ($c_data_delivery>0) && 
                    ($c_stock_move_line>0) && 
                    ($c_no_sp>0) )   { 
                        $rowCount=0;
                        
                        //delete in_delivery_subdetail row first base on no_delivery                         
                      //  $data_delete = in_delivery_subdetail_model::where('no_delivery',  $no_delivery[0]['origin'])                                             
                      //                                            ->delete();
                      
                         /*
                        if(!$data_delete){
                            $res['success']=0;    
                            $res['code']=400;                           
                            $resVal=$res;   
                        }                     
                        */    

                        foreach ($stock_move_line as  $details)
                        {
                            $Do_Detail[$rowCount]['No_Delivery']   = $no_delivery[0]['origin'];
                            $Do_Detail[$rowCount]['Kode_Gudang']   = 'GDG01';                  
                            
                            $product = $odoo->where('id','=',$details['product_id'][0])                             
                                            ->fields('default_code','barcode')
                                            ->limit(1)                             
                                            ->get('product.product');

                            $c_product = count($product);   
                            
                            if($c_product<=0){    
                               // $res['success']=0;    
                               // $res['code']=400;                 
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Product ID '.$details['product_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }   

                            $batch = $odoo->where('id','=',$details['lot_id'][0])                             
                                          ->fields('name','expiry_date')
                                          ->limit(1)                             
                                          ->get('stock.production.lot');     
                                          
                            $c_batch = count($batch);   
                            
                            if($c_batch<=0){                                     
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Lot ID '.$details['lot_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }   
              
                            

                            $Do_Detail[$rowCount]['Kode_Barang'] = $product[0]['default_code'];
                            $Do_Detail[$rowCount]['No_Batch']    = $batch[0]['name'];
                            $Do_Detail[$rowCount]['Jumlah']      = $details['qty_done'];              
                      
                            $uom_long_name =  $odoo->where('id','=',$details['product_uom_id'][0])                             
                                                   ->fields('description')
                                                   ->limit(1)                             
                                                   ->get('uom.uom');         
                                                   
                            $c_uom_long_name = count($uom_long_name);   
                            
                            if($c_uom_long_name<=0){                    
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Produk UOM ID '.$details['product_uom_id'][0].' Tidak ada di BISMySQl !'])->send(); 
                                exit;   
                            }                                                               
                            
            
                            $Do_Detail[$rowCount]['Satuan']      = $uom_long_name[0]['description']; // need long uom name
                            $Do_Detail[$rowCount]['Kadaluarsa']  = $batch[0]['expiry_date'];
                            $Do_Detail[$rowCount]['Terima']      = $details['qty_done'];
                        
                            $program_promosi = sl_surat_pesanan_detail_model::where('No_SP','=',$no_sp[0]['kode_referensi'])
                                             ->where('Kode_Barang','=',$Do_Detail[$rowCount]['Kode_Barang']) 
                                             ->where('Satuan','=',$Do_Detail[$rowCount]['Satuan'])                                    
                                             ->limit(1) 
                                             ->get('ID_Program_Promosi');    
                            
                            $c_program_promosi = count($program_promosi);  
                            
                                                
                            if($c_program_promosi>0) 
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']= $program_promosi[0]['ID_Program_Promosi'];
                            }
                            else
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']='';
                            }        
                           
                            $rowCount++;   
                        } 

                         //then replace with new Record from Odoo
                        $is_saved = in_delivery_subdetail_model::insert($Do_Detail);
                        if($is_saved)
                        {   
                            $mapping['No_Delivery'] =$no_delivery[0]['origin'];   
                            $mapping['Flag_WMS']    ='Picked';                                                                                                                                                                                                                                                                                                
                            $mapping['picking_name']=$no_delivery[0]['name'];   
                            
                            in_delivery_flag_wms_model::where('no_delivery',$mapping['No_Delivery'])
                                                      ->update([
                                                      'Flag_WMS'=>'Picked'         
                                                      ]);
           
                            //$res['success']=1;                          
                            //$res['code']=200;                          
                            //$resVal=$res;
                            response()->json(['success'=>1,
                                              'code'=>200,
                                              'message'=>'Delivery Item '.$mapping['No_Delivery'].' Berhasil di kirim ke BISMySQL !',
                                              'data'=>$Do_Detail])->send(); 
                            exit;
                        }  
                        else
                        {
                                                   
                            //$resVal=$res;
                            response()->json(['success'=>0,
                                              'code'=>400,
                                              'message'=>'Delivery Item '.$mapping['No_Delivery'].' Gagal dikirim !'])->send(); 
                            exit;
                        }                     
                }
                else 
                {
               //     $res['success']=0;    
               //     $res['code']   =400;                           
               //     $resVal=$res;
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Data Stok Picking Tidak Valid !'])->send(); 
                    exit;
                }        
        }
        catch (Exception $e) 
        {
           // $res['success']=0;    
           // $res['code']   =400;                         
            //$resVal=$res;    
            response()->json(['success'=>0,
                              'code'=>400,
                              'message'=>'Data Stock Picking Tidak Valid !'])->send(); 
            exit;        
        }
        //return response()->json([$resVal]);        
    }
    
    public function sentStockAdjusment(request $request )
    {
        #$odoo = new \Edujugon\Laradoo\Odoo();
        #$odoo = $odoo->connect();       

        #file_get_contents()

        //$adjustment_id = $request->id;
        foreach ($request as  $item_adjustment)
        {
            
        }

    }
 
    public function getUpdatePickingItem(request $request )
    {        
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();       

        $picking_id=$request->picking_id;
        try
        {
                $no_delivery = $odoo->where('id','=',$picking_id)
                                    //->where('state','=','assigned')
                                    ->Where('picking_type_id','in',[2,3])
                                   //->orWhere('picking_type_id','=',3)
                                    ->fields('origin','name')
                                    ->limit(1)                             
                                    ->get('stock.picking'); 
                
                $c_data_delivery=count($no_delivery);       
                if($c_data_delivery<=0){        
                     //$res['success']=0;    
                     //$res['code']=400;            
                     response()->json(['success'=>0,
                                       'code'=>400,
                                       'message'=>'No Delivery berdasarkan Picking ID : '.$picking_id.' Tidak ada di BISMySQL !'])->send(); 
                     exit;   
                 }    
                
                $stock_move_line = $odoo->where('picking_id.id','=',$picking_id)   
                                        ->get('stock.move.line');

                $c_stock_move_line=count($stock_move_line);
                if($c_stock_move_line<=0){       
                   // $res['success']=0;    
                    //$res['code']=400;                 
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Tidak Ada Item di stock move line !'])->send(); 
                    exit;   
                }    
                             
               
                $no_sp = in_delivery_model::where('no_delivery',$no_delivery[0]['origin'])
                                           ->select('kode_referensi')
                                           ->get(); 
                                    
                $c_no_sp = count($no_sp);           
                
                
                if($c_no_sp<=0){        
                    //$res['success']=0;    
                    //$res['code']=400;                  
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'No SP untuk DO '.$no_delivery.' Tidak Ada !'])->send(); 
                    exit;   
                }                                     
                                    
                                          
                if( ($c_data_delivery>0) && ($c_stock_move_line>0) && ($c_no_sp>0) ) { 
                        $rowCount=0;
                        $item_do=[];
                      #  $item_do_lama=[];                        
                        //delete in_delivery_subdetail row first base on no_delivery                         
                        $item_do_lama =  in_delivery_subdetail_model::where('no_delivery',  $no_delivery[0]['origin'])                               
                                                                     ->get();  
                       
                        $do_row=0;
                        foreach ($item_do_lama as  $details_do_lama[])      
                        {
                            $item_do[$do_row]['No_Delivery']        = $details_do_lama[$do_row]['No_Delivery'];
                            $item_do[$do_row]['Kode_Gudang']        = $details_do_lama[$do_row]['Kode_Gudang'];
                            $item_do[$do_row]['Kode_Barang']        = $details_do_lama[$do_row]['Kode_Barang'];
                            $item_do[$do_row]['No_Batch']           = $details_do_lama[$do_row]['No_Batch'];
                            $item_do[$do_row]['Jumlah']             = $details_do_lama[$do_row]['Jumlah'];  
                            $item_do[$do_row]['Satuan']             = $details_do_lama[$do_row]['Satuan'];  
                            $item_do[$do_row]['Kadaluarsa']         = $details_do_lama[$do_row]['Kadaluarsa'];  
                            $item_do[$do_row]['Terima']             = $details_do_lama[$do_row]['Terima'];  
                            $item_do[$do_row]['ID_Program_Promosi'] = $details_do_lama[$do_row]['ID_Program_Promosi'];  
                             
                            $do_row++;
                        }
                                                                      
                         

                        $data_delete = in_delivery_subdetail_model::where('no_delivery',  $no_delivery[0]['origin'])                                             
                                                                  ->delete();
                                                                
                      
                         /*
                        if(!$data_delete){
                            $res['success']=0;    
                            $res['code']=400;                           
                            $resVal=$res;   
                        }                     
                        */    

                        foreach ($stock_move_line as  $details)
                        {
                            $Do_Detail[$rowCount]['No_Delivery']   = $no_delivery[0]['origin'];
                            $Do_Detail[$rowCount]['Kode_Gudang']   = 'GDG01';                  
                            
                            $product = $odoo->where('id','=',$details['product_id'][0])                             
                                            ->fields('default_code','barcode')
                                            ->limit(1)                             
                                            ->get('product.product');

                            $c_product = count($product);   
                            
                            if($c_product<=0){    
                               // $res['success']=0;    
                               // $res['code']=400;                 
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Product ID '.$details['product_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }   

                            $batch = $odoo->where('id','=',$details['lot_id'][0])                             
                                          ->fields('name','expiry_date')
                                          ->limit(1)                             
                                          ->get('stock.production.lot');     
                                          
                            $c_batch = count($batch);   
                            
                            if($c_batch<=0){                                     
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Lot ID '.$details['lot_id'][0].' Tidak Ada !'])->send(); 
                                exit;   
                            }                                          

                            $Do_Detail[$rowCount]['Kode_Barang']  = $product[0]['default_code'];
                            $Do_Detail[$rowCount]['No_Batch']     = $batch[0]['name'];                           
                            /*
                            $qty_done1    = $details['level1_qty']; 
                            $qty_done2    = $details['level2_qty']; 
                            $qty_done3    = $details['level3_qty']; 
                            $qty_done4    = $details['level4_qty'];
                            */
                            $qty_done     = $details['qty_done']; 
                            /*
                            if ($qty_done1>0)
                            {
                                $qty_done=$qty_done1;
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                              ->where('Level',1)
                                                              ->select('Satuan')
                                                              ->get(); 
                            }
                            elseif ($qty_done2>0)
                            {
                                $qty_done=$qty_done2;  
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                                ->where('Level',2)
                                                                ->select('Satuan')
                                                                ->get(); 
                            }
                            elseif ($qty_done3>0)
                            {
                                $qty_done=$qty_done3;  
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                                ->where('Level',3)
                                                                ->select('Satuan')
                                                                ->get(); 
                            }
                            elseif ($qty_done4>0)
                            {
                                $qty_done=$qty_done4;  
                                $satuan =    ms_barang_satuan_model::where('Kode_Barang',$product[0]['default_code'])
                                                                ->where('Level',4)
                                                                ->select('Satuan')
                                                                ->get(); 
                            }
                             */   
                            $Do_Detail[$rowCount]['Jumlah']   = $qty_done;// $details['qty_done'];              
                      
                            $uom_long_name =  $odoo->where('id','=',$details['product_uom_id'][0])                             
                                                   ->fields('description')
                                                   ->limit(1)                             
                                                   ->get('uom.uom');         
                                                   
                            $c_uom_long_name = count($uom_long_name);   
                            
                            if($c_uom_long_name<=0){                    
                                response()->json(['success'=>0,
                                                  'code'=>400,
                                                  'message'=>'Produk UOM ID '.$details['product_uom_id'][0].' Tidak ada di BISMySQl !'])->send(); 
                                exit;   
                            }                                                               
                            
                            //$satuan['0']['Satuan'] ;
                            $Do_Detail[$rowCount]['Satuan']      = $uom_long_name[0]['description']; // need long uom name
                            $Do_Detail[$rowCount]['Kadaluarsa']  = $batch[0]['expiry_date'];
                            $Do_Detail[$rowCount]['Terima']      = $qty_done;//$details['qty_done'];
                        
                            $program_promosi = sl_surat_pesanan_detail_model::where('No_SP','=',$no_sp[0]['kode_referensi'])
                                             ->where('Kode_Barang','=',$Do_Detail[$rowCount]['Kode_Barang']) 
                                             ->where('Satuan','=',$Do_Detail[$rowCount]['Satuan'])                                    
                                             ->limit(1) 
                                             ->get('ID_Program_Promosi');    
                            
                            $c_program_promosi = count($program_promosi);                              
                                                
                            if($c_program_promosi>0) 
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']= $program_promosi[0]['ID_Program_Promosi'];
                            }
                            else
                            {
                                $Do_Detail[$rowCount]['ID_Program_Promosi']='';
                            }        
                           
                            $rowCount++;   
                        } 
                        
                         //then replace with new Record from Odoo
                        $is_saved=in_delivery_subdetail_model::insert($Do_Detail);
                        if($is_saved)
                        {   
                            $mapping['No_Delivery']  = $no_delivery[0]['origin'];   
                            $mapping['Flag_WMS']     = 'Picked';                       
                            $mapping['picking_name'] = $no_delivery[0]['name'];   
                            
                            in_delivery_flag_wms_model::where('no_delivery',$mapping['No_Delivery'])
                                                       ->update([
                                                       'Flag_WMS'=> 'Confirm',// $mapping['Flag_WMS'],
                                                       'picking_name'=>$mapping['picking_name']         
                                                      ]);
                            
                            $save_history = in_delivery_subdetail_history_model::insert($item_do);                          
           
                            //$res['success']=1;                          
                            //$res['code']=200;                          
                            //$resVal=$res;
                            response()->json(['success'=>1,
                                              'code'=>200,
                                              'message'=>'Delivery Item [ '.$mapping['No_Delivery'].', Picking ID:'.$picking_id. ' ]  Berhasil di kirim ke BISMySQL !',
                                              'data'=>$Do_Detail])->send(); 
                            exit;
                        }  
                        else
                        {
                                                   
                            //$resVal=$res;
                            response()->json(['success'=>0,
                                              'code'=>400,
                                              'message'=>'Delivery Item '.$mapping['No_Delivery'].' Gagal dikirim !'])->send(); 
                            exit;
                        }                     
                }
                else 
                {
               //     $res['success']=0;    
               //     $res['code']   =400;                           
               //     $resVal=$res;
                    response()->json(['success'=>0,
                                      'code'=>400,
                                      'message'=>'Data Stok Picking Tidak Valid !'])->send(); 
                    exit;
                }        
        }
        catch (Exception $e) 
        {
           // $res['success']=0;    
           // $res['code']   =400;                         
            //$resVal=$res;    
            response()->json(['success'=>0,
                              'code'=>400,
                              'message'=>'Data Stock Picking Tidak Valid !'])->send(); 
            exit;        
        }
        //return response()->json([$resVal]);        
    }


    
    public function postCancelPicking($no_delivery)
    {
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();   

        $no_do_adalah = substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5);

      


        $picking_id= $odoo->where('origin','=',$no_do_adalah)                             
                          ->fields('id')
                          ->limit(1)                             
                          ->get('stock.picking');

        $result = $odoo->call(
                            'stock.picking', 
                            'action_cancel',
                            [(int)$picking_id[0]['id']]             
                           );
        $result_bis=in_delivery_flag_wms_model::where('no_delivery',$no_do_adalah)
                                   ->update(['Flag_WMS'=> 'Canceled']);

        response()->json(['success'=>1])->send();             
    }

    public function postValidatePicking($no_delivery)
    {

         //$no_delivery=DOBLG20201000001
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();   

        $no_do_adalah = substr($no_delivery,0,5).'/'.substr($no_delivery,5,6).'/'.substr($no_delivery,11,5);

        $picking_id= $odoo->where('origin','=',$no_do_adalah)                             
                            ->fields('id')
                            ->limit(1)                             
                            ->get('stock.picking');       

        $result = $odoo->call(
                            'stock.picking', 
                            'button_validate',
                            [(int)$picking_id[0]['id']]             
                           );           
        
        $result_bis=in_delivery_flag_wms_model::where('no_delivery',$no_do_adalah)
                                               ->update(['Flag_WMS'=> 'Picked']);
                           
        response()->json(['success'=>1])->send();               
    }


     


}
