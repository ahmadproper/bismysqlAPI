<?php

Namespace App\Transformers;

Use App\User;
use League\Fractal\TransformerAbstract;


class StudentTransformer extends TransformerAbstract
{
    public function transform(Student $student)
     {
         return [
           'name' -> $student->nama,  
           'nrp'  -> $student->nrp,  
         ];   
     } 
}
