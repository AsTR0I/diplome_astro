<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsteriskCommand extends Model
{
    use HasFactory;

    protected $connection = 'asterisk';
    protected $table = 'asterisk_commands';
    public $timestamps = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'context',
        'command',
        'created_at',
        'updated_at',
    ];
}
