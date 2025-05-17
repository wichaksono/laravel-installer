<?php

namespace Modules\NeonWebId\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Activity Log Model
 * This model is used to record activities such as "WHO did WHAT to WHICH"
 * - WHO = causer (the actor performing the action)
 * - WHAT = description (the action performed)
 * - WHICH = subject (the object being acted upon)
 *
 * @created 2025-05-17 07:32:11
 * @author wichaksono
 * @package Modules\NeonWebId\Models
 *
 * @property int $id The unique identifier for this log entry
 * @property string|null $log_name Category of the log (e.g. 'posts', 'users')
 * @property string $description Description of the activity performed
 * @property string $subject_type Model class name of the subject
 * @property int $subject_id Primary key of the subject model
 * @property string $causer_type Model class name of the causer (usually User)
 * @property int $causer_id Primary key of the causer model
 * @property array|null $properties Additional data stored as JSON
 * @property Carbon $created_at Timestamp when the log was created
 * @property Carbon $updated_at Timestamp when the log was last updated
 */
class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the subject model
     *
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer model
     *
     * @return MorphTo
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an activity
     *
     * @param string $logName
     * @param string $description
     * @param Model|null $subject
     * @param Model|null $causer
     * @param array $properties
     *
     * @return static
     */
    public static function log(
        string $logName,
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        array $properties = []
    ): static {
        return static::create([
            'log_name'     => $logName,
            'description'  => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'causer_type'  => $causer ? get_class($causer) : null,
            'causer_id'    => $causer?->getKey(),
            'properties'   => $properties
        ]);
    }
}
