<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmail, Notifiable;

    protected $primaryKey = 'uid';

    protected $keyType = 'string';

    public $incrementing = false;

    public function getRouteKeyName()
    {
        return 'uid';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uid',
        'name',
        'email',
        'phone_number',
        'role',
        'photo',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'customer_id', 'uid');
    }

    public function queues()
    {
        return $this->hasManyThrough(
            Queue::class,
            Complaint::class,
            'customer_id',
            'complaint_number',
            'uid',
            'complaint_number'
        );
    }

    public function handledComplaints()
    {
        return $this->hasMany(Queue::class, 'mechanic_id', 'uid');
    }
}
