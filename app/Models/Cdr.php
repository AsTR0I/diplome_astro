<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    use HasFactory;

    protected $connection = 'asterisk';
    protected $table = 'cdr';
    public $timestamps = false;

    protected $fillable = [
        'calldate',
        'clid',
        'src',
        'dst',
        'dcontext',
        'channel', 
        'dstchannel',
        'lastapp','lastdata',
        'duration',
        'billsec', 
        'disposition',
        'amaflags',
        'accountcode',
        'uniqueid', 
        'userfield',
        'did',
        'recordingfile'
    ];

    // Преобразование даты в нужный формат
    public function getCalldateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d.m.Y H:i:s');
    }

    // Получаем запись по уникальному идентификатору
    public function getCallDetailsById($uniqueid)
    {
        return self::where('uniqueid', $uniqueid)->first();
    }

    // Пример использования фильтрации по статусу звонка (disposition)
    public function scopeSuccessCalls($query)
    {
        return $query->where('disposition', 'ANSWERED');
    }
        
    public function scopeFailedCalls($query)
    {
        return $query->where('disposition', 'FAILED');
    }
 

}
