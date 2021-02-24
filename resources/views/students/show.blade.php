@extends('layout/main')
@section('title','Daftar Mahasiswa')
@section('container')
   <div class="container">
        <div class="row">
          <div class="col-6"> 
              <h1 class="mt-3">Detail Students</h1>

             <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $student->nama }}</h5>
                    <h6 class="card-text">{{ $student->nrp }}</h6>
                    <h6 class="card-text">{{ $student->email }}</h6>
                    <h6 class="card-text">{{ $student->jurusan }}</h6>
                    
                     <a href="{{ $student->id }}/edit"   class="btn btn-primary">Edit</a>
                     <form action="{{ $student->id}}" method="post" class="d-inline">
                         @method('delete')
                         @csrf
                        <button type="submit" class="btn btn-danger">Delete</button>
                     </form> 
                     <a href="/students" class="card-link">Kembali</a>
                </div>
                </div>  

              
          </div>
        </div>
   </div>
@endsection
 