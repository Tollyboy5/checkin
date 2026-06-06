<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'work_date',
        'checked_in_at',
        'checked_out_at',
        'check_in_ip',
    ];

    protected $casts = [
        'work_date' => 'date',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at');
    }

    public function scopeVisibleOnDate(Builder $query, $date): Builder
    {
        $date = $date instanceof CarbonInterface
            ? $date->copy()->startOfDay()
            : Carbon::parse($date)->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        return $query->where(fn (Builder $query) => $query
            ->whereDate('work_date', $date)
            ->orWhereBetween('checked_out_at', [$date, $endOfDay])
            ->orWhere(fn (Builder $query) => $query
                ->open()
                ->where('checked_in_at', '<=', $endOfDay)
            )
        );
    }

    public function workedMinutes(): ?int
    {
        if (! $this->checked_in_at || ! $this->checked_out_at) {
            return null;
        }

        return max(0, (int) floor($this->checked_in_at->diffInMinutes($this->checked_out_at, false)));
    }

    public function workedDuration(): ?string
    {
        $minutes = $this->workedMinutes();

        if ($minutes === null) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return "{$hours}h {$remainingMinutes}m";
    }
}
