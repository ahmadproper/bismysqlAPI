<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\ms_barang_satuan_model;
use App\in_stock_opname_model;
use App\in_kartu_stok_detail_model;
use App\sy_konfigurasi_model;
use App\stockopname\in_stock_opname_awal;
use App\stockopname\in_stock_opname_hasil;
use App\stockopname\in_stock_opname_selisih;
use App\mapping\ms_odoo_map_uom_product_model;
use App\mapping\in_stock_opname_blocking_model; 
use App\mapping\ms_mapping_wh_odoo_model;
use App\stockopname\StockOpname; 
use App\stockopname\StockOpnameMapping; 
use App\stockopname\in_stok_barang_model; 
use Carbon\Carbon;
use Session;
use Illuminate\Http\Request;

class ProsesKartuStokController extends Controller
{  
    
    public function categName($categ_id,$principal_id)
    {
       $odoo = new \Edujugon\Laradoo\Odoo();
       $odoo = $odoo->connect();    
            
     
       #return $categ_id.'  '.$principal_id;

       $result_category = $odoo->Where('id','=',$categ_id)                          
                              ->fields('name')                                
                              ->get('product.category'); 

        

       $category_name = $result_category[0]['name'];

   
       
       $result_principal = $odoo->Where('id','=',$principal_id)                          
                              ->fields('barcode')                                
                              ->get('res.partner'); 

       $principal_name = $result_principal[0]['barcode'];



       return $category_name.'  '.$principal_name;

  

       

    }

    public function getLastStock(request $request)
    {
        in_stok_barang_model::where('Kode_Gudang',$request->Kode_Gudang)
                             ->select('Kode_Barang')
                             ->get('');
    }

    public function get_stock(request $request)
    {
        $odoo   = new \Edujugon\Laradoo\Odoo();
        $odoo   = $odoo->connect();   

        $result = $odoo->call(
                              'stock.inventory', 
                              'get_stock',
                              [(int)$request->adjustment_id]             
                            );     
        
        $year ='';
        $month ='';

        $year  =substr($result['date'],0,4);
        $month =substr($result['date'],5,2);
        
        return($result);
    }

    public function getNewNumber($type_nomor)
    {   
        #$search_number="KJBLG/202009/";
        #$current_date = DB::select('select LPAD(MONTH(Now()),2,"0") as Bulan ,Year(NOW()) as Tahun from dual');
        #$current_date[0] = DB::select('select now() as hariini from dual');
        $current_date_time = Carbon::now('Asia/Jakarta')->toDateTimeString();
        #return($current_date_time);
                        
        #$year  =$current_date['hariini'];
        #$month =$current_date['Bulan'];
        #$search_number='KJBLG/'.$year.$month.'/'.$no_dummy2;
         
       /*
        if($type_nomor='KJ') {
            $nomor=in_stock_opname_model::where('no_kertas_kerja','like',$search_number.'%')                                                                
                                 ->limit(1)
                                 ->select('no_kertas_kerja')
                                 ->orderBy('no_kertas_kerja', 'DESC')
                                 ->get();
                                  
            if(count($nomor)>0){
                #[(int)$picking_id]   
                $no_dummy=(int)substr( $nomor[0]['no_kertas_kerja'],13,5)+1;                               
            }
            else
            {
                $no_dummy=1;
                
            }
            
            $no_dummy2=str_pad($no_dummy, 5, "0", STR_PAD_LEFT);                 
            #$search_number='KJBLG/'.$year.$month.'/'.$no_dummy2;
            response()->json([ 
               # $search_number.'/'.$no_dummy2
               $current_date
                ])->send();  
        }
        */
        #$data =  json_decode($current_date,true,0);  
        response()->json([
            $current_date_time 
            # $search_number.'/'.$no_dummy2
            #$current_date->keyBy('hariini')          
            #$current_date->keyBy('hariini')
             ])->send();  
        #SELECT LPAD(MONTH(NOW()),2,'0') Bulan ,YEAR(NOW()) tahun       
        
    } 

    public function ClearKartuStock($no_kertas_kerja)
    {
        $hapus_dulu = in_kartu_stok_detail_model::where('no_transaksi',$no_kertas_kerja) 
                                               ->delete();        
        if($hapus_dulu)
        {
            $resval='success';
        }
        else {
            $resval='failed';
        }
        return $resval;
    }

    public function KartuStokAdjustment(Request $request)
    {      

        $no_ref = $request->No_Kertas_Kerja;
        #return $no_ref;
        #return $no_kertas_kerja; 
        #$no_ref = substr($no_kertas_kerja,0,5).'/'.substr($no_kertas_kerja,5,6).'/'.substr($no_kertas_kerja,11,5); 
        #BIS Logic, Query               
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
                                        )->get();
        
        $rowCount   = 0;
        $OpnameData = [];
        foreach ($opname as  $opnameItem)
        {
                $OpnameData[$rowCount]['Periode']         =  $opnameItem['Periode'];
                $OpnameData[$rowCount]['No_Transaksi']    =  $opnameItem['No_Kertas_Kerja'];
                $OpnameData[$rowCount]['Jenis_Transaksi'] =  $opnameItem['Jenis_Transaksi'];
                $OpnameData[$rowCount]['Tgl_Transaksi']   =  $opnameItem['Tgl_Transaksi'];    
                $OpnameData[$rowCount]['Barang']          =  $opnameItem['Kode_Barang'];        
                $OpnameData[$rowCount]['Gudang']          =  $opnameItem['Kode_Gudang'];  
                $OpnameData[$rowCount]['STATUS']          =  $opnameItem['Status_Barang'];
                $OpnameData[$rowCount]['Batch']           =  $opnameItem['No_Batch'];
                $OpnameData[$rowCount]['Level_Asal']      =  $opnameItem['Level'];
                $OpnameData[$rowCount]['Net']             =  $opnameItem['Jumlah'];
                $OpnameData[$rowCount]['Harga_Beli']      =  $opnameItem['Harga_Beli'];
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
          
        if (count($OpnameData)>0)  {  
            if($this->ClearKartuStock($no_ref)=='success')   {
                $saved = in_kartu_stok_detail_model::insert($OpnameData);
                if($saved){ 
                  return response(['success'=>1,'message'=>'Kertas Kerja dengan Nomor '.$no_ref.' Berhasil di Posting !' ]);
                }
                else {
                  return response(['success'=>0,'message'=>'Kertas Kerja dengan Nomor '.$no_ref.' Gagal di Posting !' ]);
                }
            }
        } else
        {
          return response(['success'=>0,'message'=>'Kertas Kerja dengan Nomor '.$no_ref.' Gagal di Posting !' ]);
        }
    }

    public function getStockOpname(request $request)
    {
        $odoo   = new \Edujugon\Laradoo\Odoo();
        $odoo   = $odoo->connect();   

        $result = $odoo->call(
                              'stock.inventory', 
                              'get_stock',
                              [(int)$request->adjustment_id]             
                            );                                
                   
        
        #$settime = $opname['Tanggal'];
        #return( $opname['Tanggal']);
        $year  =substr($result['date'],0,4);
        $month =substr($result['date'],5,2);
        
        #return($month.$year);                            
        /*
         'No_Kertas_Kerja', 'Kode_Principal', 'Kode_Divisi_Produk','Kode_Gudang','Tanggal',
         'Status_Barang','Status', 'User_ID', 'Time_Stamp'];                   
        */
        #header   ->whereRaw('MONTH(fecha) = ?', [06])
        $NoKJ_BIS = StockOpname::select('no_kertas_kerja')    
                                ->whereRaw('MONTH(Tanggal) = ?',$month)
                                ->whereRaw('YEAR(Tanggal) = ?',$year)                                
                                ->orderBy('no_kertas_kerja','desc')     
                                ->limit('1')
                                ->get();
        
        
        if (count($NoKJ_BIS)>0)
        {                       
            $TLast_Number = substr($NoKJ_BIS,-8,5);
        } else        
        {
            $TLast_Number = 0; 
        }       
    
        $lastNumber = $TLast_Number+1;      

        $pr_id = sprintf("%05d", $lastNumber);
   
        $branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            ->select('Nilai')
                                            ->get();
        $prefix_kj=$branchCode[0]['Nilai'];
        #return($prefix_kj);
        
        $no_kj='KJ'.$prefix_kj.'/'.$year.$month.'/'.$pr_id;
        #return($no_kj);                                    
        $opname=[];     
        $opname['No_Kertas_Kerja']    = $no_kj;
        $opname['Kode_Principal']     = 'KUI';
        $opname['Kode_Divisi_Produk'] = 'KUI';
        $opname['Kode_Gudang']        = $result['warehouse_code'];;        
        $opname['Tanggal']            = $result['date'];
        $opname['Status_Barang']      = 'BAIK';               
        $opname['Status']             = 'POST';                        
        $opname['User_ID']            = 'OdooWMS';                                
        $opname['Time_Stamp']         = Carbon::now('Asia/Jakarta');   
        
        $opname_awal    = []; /*'No_Kertas_Kerja','Kode_Barang','No_Batch','Kadaluarsa','Level','Jumlah','booked'*/
        $opname_hasil   = []; /*'No_Kertas_Kerja','Kode_Barang','No_Batch','Kadaluarsa','Level','Jumlah'*/
        $opname_selisih = []; /*'No_Kertas_Kerja','Kode_Barang','No_Batch','Kadaluarsa','Level','Jumlah','Referensi' */
        
        $row            = 0;                   
        foreach($result['adjustment_details'] as $details[]) 
        {
            $satuan = ms_odoo_map_uom_product_model::where('product_code',$details[$row]['product_code']) 
                                                   ->where('uom_short_name',$details[$row]['uom'])
                                                   ->select('uom_long_name')
                                                   ->get();

            $level = ms_barang_satuan_model::where('kode_barang',$details[$row]['product_code']) 
                                           ->where('satuan',$satuan[0]['uom_long_name'])
                                           ->select('level')
                                           ->limit(1)
                                           ->get();                                 
            #Awal
            $kadaluarsa=substr($details[$row]['batch_number'],0,4).'-'.substr($details[$row]['batch_number'],4,2).'-'.substr($details[$row]['batch_number'],6,2);
            $opname_awal[$row]['No_Kertas_Kerja'] = $no_kj;
            $opname_awal[$row]['Kode_Barang']     = $details[$row]['product_code'];
            $opname_awal[$row]['No_Batch']        = $details[$row]['batch_number'];
            $opname_awal[$row]['Kadaluarsa']      = $kadaluarsa;
            $opname_awal[$row]['Level']           = $level[0]['level'];
            $opname_awal[$row]['Jumlah']          = $details[$row]['theoretical_qty'];
            $opname_awal[$row]['booked']          = 0;

            #Hasil
            $opname_hasil[$row]['No_Kertas_Kerja'] = $no_kj;
            $opname_hasil[$row]['Kode_Barang']     = $details[$row]['product_code'];
            $opname_hasil[$row]['No_Batch']        = $details[$row]['batch_number'];
            $opname_hasil[$row]['Kadaluarsa']      = $kadaluarsa;
            $opname_hasil[$row]['Level']           = $level[0]['level'];
            $opname_hasil[$row]['Jumlah']          = $details[$row]['real_qty'];
            
            #Selisih
            #Jika diff_qyy>0
            if ((int)$details[$row]['diff_qty']>0)
            {
                $opname_selisih[$row]['No_Kertas_Kerja'] = $no_kj;
                $opname_selisih[$row]['Kode_Barang']     = $details[$row]['product_code'];
                $opname_selisih[$row]['No_Batch']        = $details[$row]['batch_number'];
                $opname_selisih[$row]['Kadaluarsa']      = $kadaluarsa;
                $opname_selisih[$row]['Level']           = $level[0]['level'];
                $opname_selisih[$row]['Jumlah']          = $details[$row]['diff_qty']; 
                $opname_selisih[$row]['Referensi']       = $details[$row]['reason'];                                            
            }
            $row++;
        };

        $saved = in_stock_opname_model::insert($opname);
        if($saved) {
            $saved_awal    = in_stock_opname_awal_model::insert($opname_awal);
            $saved_hasil   = in_stock_opname_hasil_model::insert($opname_hasil);
            $saved_selisih = in_stock_opname_selisih_model::insert($opname_selisih);            
            #$kjno_underscore = substr($no_kj
            if ($saved) {
               $updated = $odoo->where('id', $result['inventory_id'])
                               ->update('stock.inventory',['kkso_number' => $no_kj]);
               $opname_mapping['no_kertas_kerja']=$no_kj;
               $opname_mapping['inventory_id']   =$result['inventory_id'];

               StockOpnameMapping::insert($opname_mapping);
               
               if($updated){
                    response()->json([
                    'success'=>1,
                    'code'=>200,
                    'message'=>'No KKSO sudah di buat di BISMySQL dengan Nomor : '.$no_kj  ])->send(); 
               }

            }
            else
            {
                response()->json(['success'=>0,
                'code'=>400,
                'message'=>'No KKSO Gagal di proses di BISMYSQL  !'])->send(); 
            }            
        }    
      
    }
    

    public function sendStockAdjustment(request $request)
    {
        $Sudah_Pernah = StockOpnameMapping::select('no_kertas_kerja') 
                                            ->where('inventory_id','=',(int)$request->adjustment_id)
                                            ->get();        
         
        if(count($Sudah_Pernah)>0)
        {
            response()->json(['success'=>0,
                              'code'=>400,
                              'message'=>'Inventory Adjustment dengan ID : '.$request->adjustment_id.
                              ' Sudah Pernah di Proses Sebelumnya dengan No KKSO : '.$Sudah_Pernah[0]['no_kertas_kerja']])->send(); 
            exit;
        }

        $odoo   = new \Edujugon\Laradoo\Odoo();
        $odoo   = $odoo->connect();   

        $result = $odoo->call(
                              'stock.inventory', 
                              'get_stock',
                              [(int)$request->adjustment_id]             
                            );   
        $year  ='';
        $month ='';

         

        if (count($result)<=0)
        {
            response()->json([
                        'success'=>0,
                        'code'=>400,
                        'message'=>'Inventory Adjustment dengan ID : '.$request->adjustment_id.' Tidak di temukan ! '])->send(); 
                        exit;
        }

       
        $year  =substr($result['date'],0,4);
        $month =substr($result['date'],5,2);

       # return $year.' '.$month;
                
        $NoKJ_BIS = StockOpname::select('no_kertas_kerja')    
                                ->whereRaw('MONTH(Tanggal) = ?',$month)
                                ->whereRaw('YEAR(Tanggal) = ?',$year)                                
                                ->orderBy('no_kertas_kerja','desc')     
                                ->limit('1')
                                ->get();        
        
        if (count($NoKJ_BIS)>0)
        {                       
            $TLast_Number = substr($NoKJ_BIS,-8,5);
        } else        
        {
            $TLast_Number = 0; 
        }       
    
        $lastNumber = $TLast_Number+1;      

        $pr_id = sprintf("%05d", $lastNumber);
   
        $branchCode = sy_konfigurasi_model::where('Item','nocabang')
                                            ->select('Nilai')
                                            ->get();
        $prefix_kj=$branchCode[0]['Nilai'];
                
        $no_kj='KJ'.$prefix_kj.'/'.$year.$month.'/'.$pr_id;


        $kode_principals  = $odoo->Where('id','=',$result['principal_id'])                                
                                 ->fields('barcode','internal_code')                                                                   
                                 ->get('res.partner'); 

        $kode_divisi_produks = $odoo->Where('id','=',$result['product_division_id'])
                                    ->fields('name')                                                                   
                                    ->get('product.category'); 
        
        $warehouse_code =$result['warehouse_code']; 
        
        #$Kode_Gudang =$result['warehouse_code']; 

        $Mapping_Kode_Gudang          = ms_mapping_wh_odoo_model::where('wh_code','=',$warehouse_code)
                                                                 ->select('kode_gudang')
                                                                 ->get();

        $Kode_Gudang                  = $Mapping_Kode_Gudang[0]['kode_gudang'];        
        $Kode_Principal               = $kode_principals[0]['barcode'];        
        $Kode_Divisi_Produk           = $kode_divisi_produks[0]['name'];        
        

      
                
        $opname=[];            
        $opname['No_Kertas_Kerja']    = $no_kj;
        $opname['Kode_Principal']     = $Kode_Principal;
        $opname['Kode_Divisi_Produk'] = $Kode_Divisi_Produk;
        $opname['Kode_Gudang']        = $Kode_Gudang;       
        $opname['Tanggal']            = $result['date'];#Tanggal Opname Sama dengan Tanggal Adjustment Odoo
        $opname['Status_Barang']      = 'BAIK';               
        $opname['Status']             = 'POST';                        
        $opname['User_ID']            = 'OdooWMS';                                
        $opname['Time_Stamp']         = Carbon::now('Asia/Jakarta');   
        
        $opname_awal    = []; /*'No_Kertas_Kerja','Kode_Barang','No_Batch','Kadaluarsa','Level','Jumlah','booked'*/
        $opname_hasil   = []; /*'No_Kertas_Kerja','Kode_Barang','No_Batch','Kadaluarsa','Level','Jumlah'*/
        $opname_selisih = []; /*'No_Kertas_Kerja','Kode_Barang','No_Batch','Kadaluarsa','Level','Jumlah','Referensi' */
        
        $row            = 0;                   

        foreach($result['adjustment_summary'] as $details[]) 
        {
            $satuan = ms_odoo_map_uom_product_model::where('product_code',$details[$row]['product_code']) 
                                                   ->where('uom_short_name',$details[$row]['uom'])
                                                   ->select('uom_long_name','product_id')
                                                   ->limit(1)
                                                   ->get();

            $level = ms_barang_satuan_model::where('kode_barang',$details[$row]['product_code']) 
                                           ->where('satuan',$satuan[0]['uom_long_name'])
                                           ->select('level')
                                           ->limit(1)
                                           ->get();      
                        
            $lot_id       = $details[$row]['batch_id'];
            
            $expire_date  = $odoo->Where('id','=',$lot_id)
                                 ->where('product_id','=',$satuan[0]['product_id'])                                     
                                 ->fields('expiry_date')                                                                   
                                 ->get('stock.production.lot'); 
                             
            
            $kadaluarsa = $expire_date[0]['expiry_date']; 

            #Awal
            $opname_awal[$row]['No_Kertas_Kerja'] = $no_kj;
            $opname_awal[$row]['Kode_Barang']     = $details[$row]['product_code'];
            $opname_awal[$row]['No_Batch']        = $details[$row]['batch_number'];
            $opname_awal[$row]['Kadaluarsa']      = $kadaluarsa;
            $opname_awal[$row]['Level']           = $level[0]['level'];
            $opname_awal[$row]['Jumlah']          = $details[$row]['theoretical_qty'];
            $opname_awal[$row]['booked']          = 0;

            #Hasil
            $opname_hasil[$row]['No_Kertas_Kerja'] = $no_kj;
            $opname_hasil[$row]['Kode_Barang']     = $details[$row]['product_code'];
            $opname_hasil[$row]['No_Batch']        = $details[$row]['batch_number'];
            $opname_hasil[$row]['Kadaluarsa']      = $kadaluarsa;
            $opname_hasil[$row]['Level']           = $level[0]['level'];
            $opname_hasil[$row]['Jumlah']          = $details[$row]['real_qty'];
            
            #Selisih
            #Cek Ada Selisih Gak gak ?, diff_Qty  > 0            
            #hanya yang ada selisih yang Masuk ke table in_stock_opname_selisih
            if ((int)$details[$row]['diff_qty']>0)
            {
                $opname_selisih[$row]['No_Kertas_Kerja'] = $no_kj;
                $opname_selisih[$row]['Kode_Barang']     = $details[$row]['product_code'];
                $opname_selisih[$row]['No_Batch']        = $details[$row]['batch_number'];
                $opname_selisih[$row]['Kadaluarsa']      = $kadaluarsa;
                $opname_selisih[$row]['Level']           = $level[0]['level'];
                $opname_selisih[$row]['Jumlah']          = $details[$row]['diff_qty']; 
                $opname_selisih[$row]['Referensi']       = $details[$row]['reason'];                                            
            }
            $row++;
        };

        try 
        {  
            DB::beginTransaction();
            // Block Transaction
            $saved         = in_stock_opname_model::insert($opname);            
            $saved_awal    = in_stock_opname_awal::insert($opname_awal);                        
            $saved_hasil   = in_stock_opname_hasil::insert($opname_hasil);
            $saved_selisih = in_stock_opname_selisih::insert($opname_selisih);   
                
            $opname_mapping['no_kertas_kerja']= $no_kj;
            $opname_mapping['inventory_id']   = $result['inventory_id'];
            $opname_mapping['created_at']     = Carbon::now('Asia/Jakarta'); 
            StockOpnameMapping::insert($opname_mapping);                                                         

            $updated = in_stock_opname_blocking_model::where('adjustment_id',$request->adjustment_id)
                                                      ->update([
                                                     'no_kkso'=>$no_kj,                                                     
                                                     'Kode_Gudang'=>$Kode_Gudang,
                                                     'Kode_Principal'=>$Kode_Principal,
                                                     'Kode_Divisi_Produk'=>$Kode_Divisi_Produk,
                                                     'Status_Adjustment'=>'done',
                                                     'Tgl_Akhir'=>Carbon::now('Asia/Jakarta')
                                                     ]);         
                 
                                response()->json([
                                                'success'=>1,
                                                'code'=>200,
                                                'kkso_number'=>$no_kj,
                                                'message'=>'No KKSO Berhasil dibuat di BISMySQL dengan Nomor : '.$no_kj 
                                ])->send(); 
          // Jika Table table diatas Berhasil di Insert
          // Maka Simpan Semua Datanya, Kommat Kommit
           DB::commit();                             
        } catch(\Exception $e)
        {
           // Jika ada error / Salah Satu Model Gagal di insert 
           // Maka Rollback, Semua data di batalkan (Tidak jadi di Insert)
           // Berlaku untuk model yang ada di Block Transaction
           DB::rollback();
           response()->json([
                   'success'=>0,
                   'code'=>400,
                   'message'=>'Inventory Adjustment dengan ID : '.$request->adjustment_id.' Gagal di Proses ! '])->send(); 
                    exit;
        }                            
          
    }

    //Blocking Transaksi Ketika Opname
    public function FlagBlockingStock(Request $request)   
    {
        $data = [];

        $data['adjustment_id']  = $request->adjustment_id ;   
        $sudah_ada              = in_stock_opname_blocking_model::where('adjustment_id','=', (int)$data['adjustment_id'])
                                                                 ->select('adjustment_id')
                                                                 ->get();  
        #return($data['adjustment_id']);

        
        if(count($sudah_ada)<=0) {           
            if ($data['adjustment_id']) 
            {   
                $data['adjustment_id']       = (int)$data['adjustment_id'] ;                               
                $data['location_id']         = $request->location_id ;      
                $data['principal_id']        = $request->principal_id ;             
                $data['product_division_id'] = $request->product_division_id ;      
                $data['Status_Adjustment']   = 'progress'; 
                $data['Tgl_Awal']            = Carbon::now('Asia/Jakarta');          
                $data['Tgl_Akhir']           = Carbon::now('Asia/Jakarta'); 

                $odoo = new \Edujugon\Laradoo\Odoo();
                $odoo = $odoo->connect();                        
      
         
                $result_category = $odoo->Where('id','=',$data['product_division_id'])                          
                                       ->fields('name')                                
                                       ->get('product.category');    
                          
                $category_name = $result_category[0]['name'];
         
            
                
                $result_principal = $odoo->Where('id','=',$data['principal_id'])                          
                                       ->fields('barcode')                                
                                       ->get('res.partner'); 
         
                $principal_name = $result_principal[0]['barcode'];

                $data['Kode_Principal']      = $principal_name; 
                $data['Kode_Divisi_Produk']  = $category_name;      
                $Mapping_Kode_Gudang          = ms_mapping_wh_odoo_model::where('wh_id','=',$data['location_id'])
                                                                        ->select('kode_gudang')
                                                                        ->get();

                $Kode_Gudang                  = $Mapping_Kode_Gudang[0]['kode_gudang']; 
                $data['Kode_Gudang']          = $Kode_Gudang; 

                
                #return($data);  

                in_stock_opname_blocking_model::insert($data);

                response()->json([
                                'success'=>1,
                                'code'=>200,
                                'adjustment_id'=>(int)$data['adjustment_id'],
                                'message'=>'Adjustment ID '.$data['adjustment_id'].' Sukses Blocking Stok di BISMySQL !' 
                ])->send(); 
            }  
        }  else
        {
            response()->json([
                            'success'=>0,
                            'code'=>400,
                            'adjustment_id'=>(int)$data['adjustment_id'],
                            'message'=>'Blocking Stock untuk Adjustment ID '.$data['adjustment_id'].' Sudah Pernah dilakukan !' 
                            ])->send(); 
        }          
    }


    public function CancelBlockingStock(Request $request)   
    {
        $currentdata = in_stock_opname_blocking_model::where('adjustment_id',$request->adjustment_id)
                                                      ->where('Status_Adjustment','progress')
                                                      ->get();


        if(count($currentdata)>0)                                          
        {
                $data['adjustment_id'] = $request->adjustment_id ;  
                $updated = in_stock_opname_blocking_model::where('adjustment_id',$request->adjustment_id)
                                                          ->update(['Status_Adjustment'=>'cancel',
                                                                    'Tgl_Akhir'=>Carbon::now('Asia/Jakarta')
                                                                   ]);    
                if($updated){
                    response()->json([
                                    'success'=>1,
                                    'code'=>200,
                                    'adjustment_id'=>(int)$data['adjustment_id'],
                                    'message'=>'Blocking Stok untuk Adjustment ID '.$data['adjustment_id'].' Sudah di batalkan !' 
                                    ])->send(); 
                }  
                else
                {
                    response()->json([
                                    'success'=>0,
                                    'code'=>400,                
                                    'message'=>'Tidak ada Adjustment yang perlu di batalkan !' 
                                    ])->send(); 
                }
        }        
        else
        {
            response()->json([
                            'success'=>0,
                            'code'=>400,                
                            'message'=>'Tidak ada Adjustment yang perlu di batalkan !' 
                            ])->send(); 
        }

    }

    public function getStockOpnameUpdate($tgl_transaksi,$divisi_produk)
    {
        $tgl_timestamp = Carbon::parse($tgl_transaksi)->format('Y-m-d H:i:s');
        $YYMM = Carbon::parse($tgl_transaksi)->format('Ym');
        
        $data = in_kartu_stok_detail_model::where('Tgl_Transaksi',$tgl_transaksi)                                         
                                            ->where('TimeStamp','>=',$tgl_timestamp)   
                                            ->where('periode',$YYMM)    
                                            ->join('ms_barang','ms_barang.Kode_Barang','=','in_kartu_stok_detail.barang')
                                            ->where('ms_barang.kode_divisi_produk',$divisi_produk)
                                            ->distinct()                                         
                                            ->get(['Barang','Batch','Gudang']);        
        $row = 0;         
        $data_response = [];
        foreach($data as $Details[]) 
        {  
            $data_response[$row]['product_code'] = $Details[$row]['Barang'];
            $data_response[$row]['batch_number'] = $Details[$row]['Batch'];   
            $qty = in_stok_barang_model::where('in_stok_barang.Kode_Barang',$data_response[$row]['product_code'])
                                        ->where('in_stok_barang.No_Batch', $data_response[$row]['batch_number'])
                                        ->where('in_stok_barang.Kode_Gudang', $Details[$row]['Gudang'])
                                        ->where('in_stok_barang.Status','AVAILABLE')                                        
                                        ->select(                                                        
                                                 DB::raw(
                                                        'SUM( '.
                                                        ' IFNULL( '.
                                                        '    ufn_konversi_stok_level(in_stok_barang.Kode_Barang,Stok,in_stok_barang.Level,ufn_level_satuan_terkecil(in_stok_barang.Kode_Barang)),0 '.
                                                        '   ) '.
                                                        ') as Stok '
                                                        )                                                                                                                                                                                          

                                                )
                                        ->groupBy('in_stok_barang.Kode_Barang','in_stok_barang.No_Batch','in_stok_barang.Kode_Gudang')
                                        ->get();
                if(count($qty)>0)
                {
                   $data_response[$row]['qty'] =  (int)$qty[0]['Stok'];  
                }
                else
                {
                   $data_response[$row]['qty'] =  0;    
                }

            $warehouse_code = ms_mapping_wh_odoo_model::where('kode_gudang',$Details[$row]['Gudang'])
                                                       ->get('wh_code');
            
            $level = DB::select('SELECT ufn_level_satuan_terkecil("'.$data_response[$row]['product_code'].'") as level ')[0]->level;
            $data_response[$row]['uom_level'] = $level;  
            $data_response[$row]['warehouse_code'] = $warehouse_code[0]['wh_code'];                      
            $row++;
        }                                              
        return  $data_response;        
    }
        
}
