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
}

