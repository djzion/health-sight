<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AssessmentPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quarter',
        'year',
        'start_date',
        'end_date',
        'description',
        'assessment_type',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Relationships
     */
    public function assessmentResponses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    /**
     * Scopes
     */
    public function scopeGeneral($query)
    {
        return $query->where('assessment_type', 'general');
    }

    public function scopeSafecare($query)
    {
        return $query->where('assessment_type', 'safecare');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
    }

    /**
     * Static methods for getting periods
     */
    public static function getCurrentGeneralPeriod()
    {
        return static::general()
                     ->current()
                     ->first();
    }

    public static function getCurrentSafecarePeriod()
    {
        return static::safecare()
                     ->current()
                     ->first();
    }

    public static function getNextGeneralPeriod()
    {
        $now = now();
        return static::general()
                     ->where('is_active', true)
                     ->where('start_date', '>', $now)
                     ->orderBy('start_date')
                     ->first();
    }

    public static function getNextSafecarePeriod()
    {
        $now = now();
        return static::safecare()
                     ->where('is_active', true)
                     ->where('start_date', '>', $now)
                     ->orderBy('start_date')
                     ->first();
    }

    /**
     * Accessor methods
     */
    public function getStatusAttribute()
    {
        $now = now();

        if (!$this->is_active) {
            return 'inactive';
        }

        if ($now->lt($this->start_date)) {
            return 'upcoming';
        }

        if ($now->gt($this->end_date)) {
            return 'expired';
        }

        return 'active';
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->status !== 'active') {
            return null;
        }

        return now()->diffInDays($this->end_date, false);
    }

    public function getDaysUntilStartAttribute()
    {
        if ($this->status !== 'upcoming') {
            return null;
        }

        return now()->diffInDays($this->start_date, false);
    }

    /**
     * Helper methods
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isUpcoming()
    {
        return $this->status === 'upcoming';
    }

    public function isExpired()
    {
        return $this->status === 'expired';
    }

    public function hasStarted()
    {
        return now()->gte($this->start_date);
    }

    public function hasEnded()
    {
        return now()->gt($this->end_date);
    }

    /**
     * Create a new assessment period
     */
    public static function createPeriod($data)
    {
        return static::create([
            'name' => $data['name'],
            'quarter' => $data['quarter'] ?? null,
            'year' => $data['year'] ?? now()->year,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'description' => $data['description'] ?? null,
            'assessment_type' => $data['assessment_type'] ?? 'general',
            'is_active' => $data['is_active'] ?? true
        ]);
    }

    /**
     * Activate this period and deactivate others of the same type
     */
    public function activate()
    {
        // Deactivate other periods of the same type
        static::where('assessment_type', $this->assessment_type)
              ->where('id', '!=', $this->id)
              ->update(['is_active' => false]);

        // Activate this period
        $this->update(['is_active' => true]);

        return $this;
    }

    /**
     * Deactivate this period
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    /**
     * Check if user can submit assessments in this period
     */
    public function canSubmitAssessments()
    {
        return $this->isActive() && $this->hasStarted() && !$this->hasEnded();
    }

    /**
     * Format period name for display
     */
    public function getDisplayNameAttribute()
    {
        if ($this->quarter) {
            return "Q{$this->quarter} {$this->year} - {$this->name}";
        }

        return "{$this->year} - {$this->name}";
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute()
    {
        return $this->start_date->format('M j, Y') . ' - ' . $this->end_date->format('M j, Y');
    }
}
