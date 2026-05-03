<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Symptom extends Model
{
    protected $primaryKey = 'symptom_code';

    protected $keyType = 'string';

    public $incrementing = false;

    public function getRouteKeyName()
    {
        return 'symptom_code';
    }

    protected $fillable = [
        'symptom_code',
        'name',
    ];

    public function damages(): BelongsToMany
    {
        return $this->belongsToMany(
            Damage::class,      // Model tujuan
            'rules',            // Nama tabel pivot
            'symptom_code',     // Foreign key di tabel rules untuk model ini
            'damage_code',      // Foreign key di tabel rules untuk model tujuan
            'symptom_code',     // Local key di tabel symptoms
            'damage_code'       // Local key di tabel damages
        );
    }
}
