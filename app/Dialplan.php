<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dialplan extends Model
{
    protected $connection = 'asterisk';
    protected $table = 'dialplan';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'destination',
        'context',
        'priority',
        'action',
        'params',
        'description',
    ];
}
