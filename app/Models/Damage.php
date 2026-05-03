<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Damage extends Model
{
    protected $primaryKey = 'damage_code';

    protected $keyType = 'string';

    public $incrementing = false;

    public function getRouteKeyName()
    {
        return 'damage_code';
    }

    protected $fillable = [
        'damage_code',
        'name',
    ];

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(
            Symptom::class,     // Model tujuan
            'rules',            // Nama tabel pivot
            'damage_code',      // Foreign key di tabel rules untuk model ini
            'symptom_code',     // Foreign key di tabel rules untuk model tujuan
            'damage_code',      // Local key di tabel damages
            'symptom_code'      // Local key di tabel symptoms
        );
    }
}
