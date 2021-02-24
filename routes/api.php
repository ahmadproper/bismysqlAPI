<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/ 
//route module stock picking BLG
Route::get('blg/getUpdatePickingItem/{picking_id}','BISAPIController@getUpdatePickingItem'); 
Route::get('blg/getmacpicking_item/{picking_id}/{direction}','StockPickingController@getmacpicking_item');
Route::post('blg/get_faktur_retur','StockPickingController@get_faktur_retur');
Route::post('blg/get_detail_po','StockPickingController@get_detail_po');   

Route::post('blg/act_afterconfirm_stockrequest','StockPickingController@act_afterconfirm_stockrequest');

Route::post('blg/postCancelPicking/{no_delivery}','BISAPIController@postCancelPicking');   
Route::post('blg/postValidatePicking/{no_delivery}','BISAPIController@postValidatePicking');   
//route module Stock Adjustment BLG
Route::get('blg/sendStockAdjustment/{adjustment_id}','ProsesKartuStokController@sendStockAdjustment');   
Route::get('blg/getStockOpname/{adjustment_id}','ProsesKartuStokController@getStockOpname'); 
Route::get('blg/getStockOpnameUpdate/{tgl_transaksi}/{divisi_produk}','ProsesKartuStokController@getStockOpnameUpdate'); 
//Hanya Untuk Testing
Route::get('blg/get_stock/{adjustment_id}','ProsesKartuStokController@get_stock'); 
Route::get('blg/getcategname/{categ_id}/{principal_id}','ProsesKartuStokController@categName');   
//Blocking Stok BISMySQL from Odoo
Route::post('blg/flagBlockingStock/{adjustment_id}','ProsesKartuStokController@FlagBlockingStock');   
Route::post('blg/cancelBlockingStock/{adjustment_id}','ProsesKartuStokController@CancelBlockingStock');   
//Cek Status Adjustment BLG
Route::post('blg/cekOpnameStatus','OdooBISOpnameController@cekOpnameStatus');
Route::get('blg/cekDivisiProdukOpname/{Kode_Divisi_Produk}','OdooBISOpnameController@cekDivisiProdukOpname');
//Sequence numbering BISMySQL Version
Route::get('blg/getNewNumber/{type_nomor}/{tanggal_transaksi}','SequenceController@getNewNumber');
//create kkso BLG
Route::get('blg/createadjustment','StockOpnameController@createkkso');
//Bridging BISMySQL Peminjaman, Pengembalian & Pemfakturan Kanvas dari Odoo 
Route::post('blg/postPickingSpreading/{picking_id}','SpreadingController@postPickingSpreading');
Route::post('blg/postPengembalianBarang/{picking_id}','SpreadingController@postPengembalianBarang');
Route::post('blg/postPemfakturanKanvas/{order_id}','SpreadingController@postPemfakturanKanvas');
Route::post('blg/KartuStokAdjustment','ProsesKartuStokController@KartuStokAdjustment');

Route::get('blg/getReceiving/{no_bpb}/{no_do}','PurchaseReceiveController@getReceiving');
Route::get('blg/getListReceiving','PurchaseReceiveController@getListReceiving');

