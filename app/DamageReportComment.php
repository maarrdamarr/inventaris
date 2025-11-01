<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageReportComment extends Model
{
    protected $guarded = [];

    public function damage_report()
    {
        return $this->belongsTo(DamageReport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

