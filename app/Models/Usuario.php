<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public $searchable = [
        'id',
        'apodo',
        'contrasenha'
    ];
    protected $table = 'usuarios';
    protected $fillable = [
        'apodo',
        'contrasenha',
        'rol'
    ];

}
