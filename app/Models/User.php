<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
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

    public function competitors(): HasMany
    {
        return $this->hasMany(Competitor::class, 'wca_id', 'wca_id');
    }

    public function staffs(): HasMany
    {
        return $this->hasMany(Staff::class, 'wca_id', 'wca_id');
    }

    public function getCompetitionsAttribute(): Collection
    {
        $competitionIds = $this->competitors->map(Fn (Competitor $c) => $c->competition_id);
        $competitionIds->union($this->staffs->map(Fn (Staff $s) => $s->competition_id));
        return $competitionIds->map(Fn ($id) => Competition::find($id));
    }

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
