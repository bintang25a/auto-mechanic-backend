<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = ['symptom_code', 'damage_code'];

    public function symptom()
    {
        return $this->belongsTo(Symptom::class, 'symptom_code', 'symptom_code');
    }

    public function damage()
    {
        return $this->belongsTo(Damage::class, 'damage_code', 'damage_code');
    }
}
