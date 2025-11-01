<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageReportFile extends Model
{
    protected $guarded = [];

    public function damage_report()
    {
        return $this->belongsTo(DamageReport::class);
    }
}

