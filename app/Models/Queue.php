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
        'mechanic_id',
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

            $todayCount = self::query()->whereDate('created_at', '=', now()->toDateString(), 'and')->count();

            $dayOfMonth = (int) now()->format('j');

            $dateLetter = self::convertToAlpha($dayOfMonth);

            $queue->queue_number =
                $dateLetter.
                '-'.
                str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    private static function convertToAlpha(int $number)
    {
        $alphabet = '';
        while ($number > 0) {
            $modulo = ($number - 1) % 26;
            $alphabet = chr(65 + $modulo).$alphabet;
            $number = (int) (($number - $modulo) / 26);
        }

        return $alphabet;
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id', 'uid');
    }

    public function complaint()
    {
        return $this->hasOne(Complaint::class, 'queue_id', 'id');
    }
}
