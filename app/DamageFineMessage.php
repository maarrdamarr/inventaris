<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageFineMessage extends Model
{
    protected $guarded = [];

    public function report() { return $this->belongsTo(DamageReport::class, 'damage_report_id'); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function recipient() { return $this->belongsTo(User::class, 'recipient_id'); }
}

