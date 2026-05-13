<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $table = 'queues';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'queue_number',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($queue) {

            $lastQueue = self::orderBy('id', 'desc')->first();

            if (! $lastQueue) {
                $number = 1;
            } else {
                $lastNumber = (int) substr($lastQueue->id, 2);
                $number = $lastNumber + 1;
            }

            $queue->id = 'Q-'.str_pad($number, 5, '0', STR_PAD_LEFT);

            $todayCount = self::whereDate('created_at', now()->toDateString())
                ->count();

            $queue->queue_number =
                'Q-'.
                now()->format('Ymd').
                '-'.
                str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id', 'uid');
    }

    public function complaint()
    {
        return $this->hasOne(Complaint::class);
    }
}
