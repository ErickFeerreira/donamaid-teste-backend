<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adress extends Model
{
    protected $fillable = ['rua', 'numero','complemento', 'cep', 'cidade', 'estado', 'pais'];

}
