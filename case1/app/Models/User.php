<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return ['role'=>$this->role];
    }
    protected $fillable = [
        'name',
        'email',
        'age',
        'dist_country',
        'gender',
        'role',
        'phone_num',
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
    public function contactMessages()
    {
    return $this->hasMany(ContactMessage::class);
    }
   public function trips()
{
    return $this->hasMany(Trip::class);
}
    public function bookings()
{
    return $this->hasMany(Booking::class);
}
public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class, 'tour_guide_id');
    }

}
