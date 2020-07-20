<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['horario_oficial', 'dia', 'duracao', 'endereco', 'cliente', 'profissional'];


}
