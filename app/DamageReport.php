<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageReport extends Model
{
    protected $guarded = [];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function files()
    {
        return $this->hasMany(DamageReportFile::class);
    }

    public function comments()
    {
        return $this->hasMany(DamageReportComment::class)->latest();
    }
}
