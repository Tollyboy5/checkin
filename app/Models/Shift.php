<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'starts_at',
        'ends_at',
        'grace_minutes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'starts_at' => 'datetime:H:i',
        'ends_at' => 'datetime:H:i',
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function rosters(): HasMany
    {
        return $this->hasMany(Roster::class);
    }
}
