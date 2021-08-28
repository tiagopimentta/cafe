<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Garrafa extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'capacidade_total', 'quantidade_atual', 'capacidade_xicara',
    ];
}
