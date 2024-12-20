<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    public $searchable = [
        'id',
        'apodo',
        'rol'
    ];
    protected $table = 'usuarios';
    protected $fillable = [
        'apodo',
        'contrasenha',
        'rol'
    ];
    protected $hidden = [
        'contrasenha',
    ];
}
