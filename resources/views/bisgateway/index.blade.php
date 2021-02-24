@extends('layout/main')
@section('title','Daftar PO BISMySQL')
@section('container')
   <div class="container">
        <div class="row">
          <div class="col-10"> 
              <h1 class="mt-3">Daftar PO</h1>
              <table class="table">
                  <thead class="thead-dark"> 
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">No PO</th>
                    <th scope="col">Tgl PO</th>
                    <th scope="col">Kode Principal</th>                     
                    <th scope="col">Aksi</th>

                    </tr>
                    </thead>
                    <tbody>
                       @foreach($purchase_order as $purchase)
                       <tr>
                         <th scope="row">{{$loop->iteration}}</th>
                         <td>{{$purchase->No_PO}}</td>
                         <td>{{$purchase->Tgl_PO}}</td>
                         <td>{{$purchase->Kode_Principal}}</td>
                          
                         <td>
                           <a href="" class="badge badge-primary">Sync</a>
                           <a href="" class="badge badge-success">Edit</a>
                           <a href="" class="badge badge-danger">Delete</a>
                         </td>
                       </tr>
                       @endforeach
                    </tbody>

              </table>
          </div>
        </div>
   </div>
@endsection
 