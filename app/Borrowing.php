<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $guarded = [];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

