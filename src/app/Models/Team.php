<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
//    use HasUuids;

    protected $fillable = ['group_id', 'name', 'flag_emoji', 'played', 'points'];

    // Relations
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }

    // Helpers
    public function displayName(): string
    {
        $name = __('countries.' . $this->name);
        return $name;
        // return $this->flag_emoji
        //     ? "{$this->flag_emoji} {$name}"
        //     : $name;
    }

    public function oddsApiName(): string
    {
        return __("countries.{$this->name}", [], 'en') ?: $this->name;
    }
}
