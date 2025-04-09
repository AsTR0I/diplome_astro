<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $connection = 'asterisk';
    protected $table = 'extensions';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'context',
        'exten',
        'priority',
        'app',
        'appdata',
    ];
}
