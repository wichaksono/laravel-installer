<?php

namespace Modules\NeonWebId\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\NeonWebId\Models\ActivityLog;
use RuntimeException;

/**
 * HasActivityLogs Trait
 * This trait provides activity logging functionality to Eloquent models
 *
 * @created 2025-05-17 07:41:04
 * @author wichaksono
 * @package Modules\NeonWebId\Traits
 *
 * @mixin Model
 * @method array getOriginal() Get the model's original attribute values
 * @method array getAttributes() Get the model's current attribute values
 * @method mixed getKey() Get the value of the model's primary key
 * @method array getDirty() Get the attributes that have been changed since last sync
 * @method bool isDirty() Determine if the model or any of the given attribute(s) have been modified
 */
trait HasActivityLogs
{
    /**
     * Get all activity logs where this model is the subject
     *
     * @return MorphMany
     * @throws RuntimeException if the trait is not used on an Eloquent model
     */
    public function activities(): MorphMany
    {
        if ( ! ($this instanceof Model)) {
            throw new RuntimeException('HasActivityLogs trait can only be used on Eloquent models.');
        }

        return $this->morphMany(ActivityLog::class, 'subject');
    }

    /**
     * Log an activity for this model
     *
     * @param string $logName Category of the log (e.g., 'posts', 'users')
     * @param string $description Description of what happened
     * @param array $properties Additional data to store
     *
     * @return ActivityLog
     * @throws RuntimeException if the trait is not used on an Eloquent model
     */
    public function logActivity(string $logName, string $description, array $properties = []): ActivityLog
    {
        if ( ! ($this instanceof Model)) {
            throw new RuntimeException('HasActivityLogs trait can only be used on Eloquent models.');
        }

        return ActivityLog::create([
            'log_name'     => $logName,
            'description'  => $description,
            'subject_type' => get_class($this),
            'subject_id'   => $this->getKey(),
            'causer_type'  => auth()->check() ? get_class(auth()->user()) : null,
            'causer_id'    => auth()->id(),
            'properties'   => $properties
        ]);
    }

    /**
     * Log model changes
     *
     * @param string $logName Category of the log
     * @param string $description Description of what happened
     * @param array|null $oldAttributes Previous attributes (optional)
     * @param array|null $newAttributes Current attributes (optional)
     *
     * @return ActivityLog
     * @throws RuntimeException if the trait is not used on an Eloquent model
     */
    public function logChanges(
        string $logName,
        string $description,
        ?array $oldAttributes = null,
        ?array $newAttributes = null
    ): ActivityLog {
        if ( ! ($this instanceof Model)) {
            throw new RuntimeException('HasActivityLogs trait can only be used on Eloquent models.');
        }

        return $this->logActivity($logName, $description, [
            'old'     => $oldAttributes ?? $this->getOriginal(),
            'new'     => $newAttributes ?? $this->getAttributes(),
            'changes' => $this->getDirty()
        ]);
    }

    /**
     * Boot the trait
     * Automatically log model events if enabled
     *
     * @return void
     */
    protected static function bootHasActivityLogs(): void
    {
        if ( ! is_subclass_of(static::class, Model::class)) {
            throw new RuntimeException(
                sprintf(
                    'HasActivityLogs trait can only be used on Eloquent models. %s is not a model.',
                    static::class
                )
            );
        }

        // Log when model is created
        static::created(function ($model) {
            if (method_exists($model, 'shouldLogActivity') && ! $model->shouldLogActivity('created')) {
                return;
            }

            $model->logActivity(
                strtolower(class_basename($model)),
                'created',
                ['attributes' => $model->getAttributes()]
            );
        });

        // Log when model is updated
        static::updated(function ($model) {
            if (method_exists($model, 'shouldLogActivity') && ! $model->shouldLogActivity('updated')) {
                return;
            }

            if ( ! $model->isDirty()) {
                return;
            }

            $model->logChanges(
                strtolower(class_basename($model)),
                'updated'
            );
        });

        // Log when model is deleted
        static::deleted(function ($model) {
            if (method_exists($model, 'shouldLogActivity') && ! $model->shouldLogActivity('deleted')) {
                return;
            }

            $model->logActivity(
                strtolower(class_basename($model)),
                'deleted',
                ['attributes' => $model->getAttributes()]
            );
        });
    }
}
