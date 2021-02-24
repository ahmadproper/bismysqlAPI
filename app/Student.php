<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
  //use SoftDeletes; //tidak di hapus di database
  protected $fillable=['nama','nrp','email','jurusan']; 
  //Fillable boleh diisi, jika tidak disebutkan disini maka Field request post tidak akan bisa diisi 
  //kebalikannya $guarded  (tidak boleh);
}
