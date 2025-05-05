<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipPacket extends Model
{
    use HasFactory;

    protected $connection = 'asterisk';
    protected $table = 'sip_packets';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'call_id',
        'source_ip',
        'dest_ip',
        'method',
        'full_packet',
        'captured_at',
    ];
}
