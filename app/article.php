<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class article extends Model
{
    protected $fillable = [
        'fichier_id', 'user_id', 'description', 'titre'
    ];
}
