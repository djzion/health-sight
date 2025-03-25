<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Safecare extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'safecare';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_no',
        'question_description',
        'question_short',
        'section',
        'status'
    ];

    /**
     * Get the responses for this safecare question.
     */
    public function responses()
    {
        return $this->hasMany(SafecareResponses::class, 'safecare_id');
    }

    /**
     * Scope a query to only include active questions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by section.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $section
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSection($query, $section)
    {
        return $query->where('section', $section);
    }
}
