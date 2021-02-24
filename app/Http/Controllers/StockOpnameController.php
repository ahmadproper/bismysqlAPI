<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\stockopname\StockOpname;
use App\stockopname\in_stock_opname_awal;
use App\stockopname\in_stock_opname_hasil;
use App\stockopname\in_stock_opname_selisih;
use App\stockopname\StockOpnameMapping;
use Carbon\Carbon;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $stock_header= StockOpname::paginate(20);
        $stock_header= StockOpname::orderBy('no_kertas_kerja','desc')->paginate(20);

        return $stock_header;
    }

    public function createkkso()
    {
        
        //stok opname//
        // get last nomor KJ BIS
        $kj_bis = StockOpname::select('no_kertas_kerja')
                               ->orderBy('no_kertas_kerja','desc')
                               ->limit('1')
                               ->get();
        $cupangserit= substr($kj_bis,-8,5);
        
        //=================//
        //      Base       //
        //=================//
        //date carbon
        $settime= Carbon::now();
        $th=$settime->format('Y');
        $mont=$settime->format('m');
        
        //build KJ from odoo
        $prefix_kj='KJBLG';
        $postfix=$cupangserit+1;
        $pr_id = sprintf("%05d", $postfix);
        $kj_odoo=$prefix_kj.'/'.$th.$mont.'/'.$pr_id;
        //$no_kertas_kerja=$prefix_kj.'/'.$th.$mont.'/'.$postfix;

        # transfer inventory from odoo to bis mysql
        $odoo = new \Edujugon\Laradoo\Odoo();
        $version = $odoo->version();
        $odoo = $odoo->connect();    
             
        $inventorys = $odoo->call('stock.inventory','get_stock',[74]);
        //$product_div=$inventorys['items'][0]['product_code'];
         $princ_code=$inventorys['product_division'];

        //=================//
        //stock_opname//
        //=================//
        //$items= $inventorys['inventory_name'];
         $opname = new StockOpname();
         $opname->no_kertas_kerja = $kj_odoo;
         $opname->kode_principal  = substr($princ_code,0,3);
         $opname->kode_gudang     = $inventorys['warehouse_code'];
         $opname->tanggal         = $inventorys['date'];
         $opname->save();
        
        //=================//
        //stock_opname_awal//
        //=================//
        $inventorys=$inventorys['adjustment_details'];
        $rowItems  =0;
        $inventory =[];
        foreach ($inventorys as $inventory[]) {
            # code...
            $stockopnameawal[$rowItems]['no_kertas_kerja']=$kj_odoo;
            $stockopnameawal[$rowItems]['kode_barang']=$inventory[$rowItems]['product_code'];
            $stockopnameawal[$rowItems]['no_batch']=$inventory[$rowItems]['batch_number'];
            $stockopnameawal[$rowItems]['kadaluarsa']=Carbon::now('Asia/Jakarta');
            $stockopnameawal[$rowItems]['level']='';
            $stockopnameawal[$rowItems]['jumlah']=$inventory[$rowItems]['theoretical_qty'];
            $stockopnameawal[$rowItems]['booked']='';
            $rowItems++;
        }
        $save_awal = in_stock_opname_awal::insert($stockopnameawal);

        //=================//
        //stock_opname_hasil//
        //=================//
       // $inventorys=$inventorys['adjustment_details'];
        $rowItems=0;
        $inventory=[];
        foreach ($inventorys as $inventory[]) {
            # code...
            $stockopnamehasil[$rowItems]['no_kertas_kerja']=$kj_odoo;
            $stockopnamehasil[$rowItems]['kode_barang']= $inventory[$rowItems]['product_code'];
            $stockopnamehasil[$rowItems]['no_batch']   = $inventory[$rowItems]['batch_number'];
            $stockopnamehasil[$rowItems]['kadaluarsa'] = Carbon::now('Asia/Jakarta');
            $stockopnamehasil[$rowItems]['level']      = '';
            $stockopnamehasil[$rowItems]['jumlah']     = $inventory[$rowItems]['real_qty'];
            $rowItems++;
        }
        $save_hasil=in_stock_opname_hasil::insert($stockopnamehasil);
        //=================//
        //stock_opname_hasil//
        //=================//
       // $inventorys=$inventorys['adjustment_details'];
       $rowItems=0;
       $inventory=[];
       foreach ($inventorys as $inventory[]) {
           # code...
           $stockopnameselisih[$rowItems]['no_kertas_kerja']=$kj_odoo;
           $stockopnameselisih[$rowItems]['kode_barang']    =$inventory[$rowItems]['product_code'];
           $stockopnameselisih[$rowItems]['no_batch']       =$inventory[$rowItems]['batch_number'];
           $stockopnameselisih[$rowItems]['kadaluarsa']     =Carbon::now('Asia/Jakarta');
           $stockopnameselisih[$rowItems]['level']          ='';
           $stockopnameselisih[$rowItems]['jumlah']         =$inventory[$rowItems]['diff_qty'];
           $rowItems++;
       }
       $save_selisih=in_stock_opname_selisih::insert($stockopnameselisih);

       return $stockopnameawal;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $StockHeader = new StockOpname();
        $StockHeader->no_kertas_kerja = $request->no_kertas_kerja;
        $StockHeader->kode_principal = $request->kode_principal;
        $StockHeader->kode_divisi_produk = $request->kode_divisi_produk;
        $StockHeader->kode_gudang = $request->kode_gudang;
        $StockHeader->tanggal = $request->tanggal;
        $StockHeader->status = $request->status;
        $StockHeader->no_kertas_kerja = $request->no_kertas_kerja;
        $StockHeader->user_id = 'Odoo';
        $StockHeader->save();

        $stock_maping = new StockOpnameMapping();
        $stock_maping->no_kertas_kerja = $request->no_kertas_kerja;
        $stock_maping->id_adjustment = $request->id_adjustment;
        $stock_maping->save();
        
        return ('Succsess');
    }

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
