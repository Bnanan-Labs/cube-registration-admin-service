<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\HasMany;


class StaffRole extends Model
{
    use HasFactory, Uuid;

    /**
     * @return HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(Staff::class, 'member_id');
    }
}
