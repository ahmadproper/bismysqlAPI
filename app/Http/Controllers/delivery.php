<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DoHeader;
use App\DoDetail;

class delivery extends Controller
{
    public function update(Request $request, $id)
    {
            
        $no= substr($id,0,5).'/'.substr($id,5,6).'/'.substr($id,-5);
        // Update the order
    
        //delete existing do
        $deliverydell = DoDetail::where('no_delivery', $no);
        $deliverydell->delete();

        
        $doUpdate = $request->all();
        $doDetail = DoDetail::where('no_delivery',$no);
        $doDetail->update($doUpdate);

        $res['message']='Success';
        $res['code']='200';
        return $res;        
    }
}
