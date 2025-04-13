<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sippeer extends Model
{
    protected $connection = 'asterisk';
    protected $table = 'sippeers';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'type',
        'secret',
        'host',
        'context',
        'nat',
        'directmedia',
        'ipaddr',
        'port',
        'allow',
    ];
}
