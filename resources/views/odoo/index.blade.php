@extends('layout/main')
@section('title','Daftar Purchase Odoo')
@section('container')
   <div class="container">
        <div class="row">
          <div class="col-6"> 
              <h1 class="mt-3">Purchase Order List</h1>
              <table class="table">
                    <thead class="thead-dark"> 
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">No PO</th>
                        <th scope="col">Purchase Order Date</th>
                        <th scope="col">Action</th> 
                      </tr>
                    </thead>
                    <tbody>              
                       @foreach($purchases as $purchase)
                       <tr>
                         <th scope="row">{{$loop->iteration}}</th>
                         <td>{{$purchase['name']}}</td>
                         <!--  <td>{{$purchase['id']}}</td> -->
                         <td>{{$purchase['date_order']}}</td>                       
                         <td>
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
 