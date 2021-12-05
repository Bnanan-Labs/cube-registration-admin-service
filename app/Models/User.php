<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getWcaAttribute(): object
    {
        return json_decode($this->raw);
    }

    public function getIsCompetitorAttribute(): bool
    {
        return $this->wca_id && Competitor::where('wca_id', $this->wca_id)->exists();
    }

    public function getIsStaffAttribute(): bool
    {
        return $this->wca_id && Staff::where('wca_id', $this->wca_id)->exists();
    }

    public function getIsSpectatorAttribute(): bool
    {
        return true;
    }
}
