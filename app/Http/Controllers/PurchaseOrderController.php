<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\pc_purchase_order_model;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function jenkins()
    {
      # code...
      return 'this is automation Server Update succesfully';
    }
    public function index()
    {
        
      # return 'helo';
      # memanggil model Student.php masukan ke variable students
      #$users = User::where('votes', '>', 100)->paginate(15);
    
 
      // mengirim data pegawai ke view index
    
       $purchase_order = DB::table('pc_purchase_order')->paginate(15);
       return view('bisgateway.index', ['purchase_order' => $purchase_order]);
 
  
    }
    
}
