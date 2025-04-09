<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait PrimaryUuid
{
    /**
     * The "booted" method of the model.
     *
     * @param  \App\SipTrunk  $sipTrunk
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
