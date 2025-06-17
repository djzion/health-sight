<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafecarePeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'quarter',
        'year',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'year' => 'integer'
    ];

    /**
     * Get all SafeCare assessments for this period
     */
    public function safecareAssessments(): HasMany
    {
        return $this->hasMany(SafecareAssessment::class, 'safecare_period_id');
    }

    /**
     * Get the user who created this period
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the period is currently active (within date range)
     */
    public function isCurrentlyActive(): bool
    {
        $now = now();
        return $this->is_active &&
               $now->greaterThanOrEqualTo($this->start_date) &&
               $now->lessThanOrEqualTo($this->end_date);
    }

    /**
     * Get the period status as a readable string
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $now = now();

        if ($now->lessThan($this->start_date)) {
            return 'Upcoming';
        } elseif ($now->greaterThan($this->end_date)) {
            return 'Expired';
        } else {
            return 'Active';
        }
    }

    /**
     * Get assessments count for this period
     */
    public function getAssessmentsCountAttribute(): int
    {
        return $this->safecareAssessments()->count();
    }

    /**
     * Get average compliance for this period
     */
    public function getAverageComplianceAttribute(): float
    {
        return $this->safecareAssessments()->avg('compliance_percentage') ?? 0;
    }

    /**
     * Scope to get active periods only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get current periods (within date range and active)
     */
    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Scope to get periods by quarter and year
     */
    public function scopeByQuarter($query, $quarter, $year = null)
    {
        $query->where('quarter', $quarter);

        if ($year) {
            $query->where('year', $year);
        }

        return $query;
    }

    /**
     * Scope to get periods by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }
}
