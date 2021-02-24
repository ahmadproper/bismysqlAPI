@extends('layout/main')
@section('title','Edit Students Data')
@section('container')
   <div class="container">
        <div class="row">
          <div class="col-6"> 
              <h1 class="mt-3">Edit Students</h1>
              <form method="post" action="/students/{{$student->id}}"> 
               @method('patch')
               @csrf
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" 
                    placeholder="Masukan Nama" name="nama" value="{{ $student->nama }}">
                    <div class="invalid-feedback">Please Insert Name on this Field</div>
                </div>
                <div class="form-group">
                    <label for="nrp">NRP</label>
                    <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp" placeholder="Masukan NRP" name="nrp" 
                    value="{{$student->nrp}}">
                    <div class="invalid-feedback">NRP Field must be 10 Length</div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" placeholder="Masukan Email"
                     name="email" value="{{$student->email}}">
                </div>
                
                
                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <input type="text" class="form-control" id="jurusan" placeholder="Masukan Jurusan" 
                    name="jurusan" value="{{$student->jurusan}}">
                </div>
                <button type="submit" class="btn btn-primary">Edit</button>
                <a href="/students/{{$student->id}}" class="card-link">Kembali</a>
                
</form>

              
          </div>
        </div>
   </div>
@endsection
 