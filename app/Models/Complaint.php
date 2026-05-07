<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $primaryKey = 'complaint_number';

    protected $keyType = 'string';

    public $incrementing = false;

    public function getRouteKeyName()
    {
        return 'complaint_number';
    }

    protected $fillable = [
        'complaint_number',
        'customer_id',
        'queue_id',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {

            $lastComplaint = self::orderBy('complaint_number', 'desc')->first();

            if (!$lastComplaint) {
                $number = 1;
            } else {
                $lastNumber = (int) substr($lastComplaint->complaint_number, 3);
                $number = $lastNumber + 1;
            }

            $complaint->complaint_number = 'CMP' . str_pad($number, 5, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'uid');
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function symptoms()
    {
        return $this->belongsToMany(
            Symptom::class,
            'complaint_symptom',
            'complaint_number',
            'symptom_code',
            'complaint_number',
            'symptom_code'
        );
    }
}
