<?php

namespace App\Models\System\Traits;

/**
 * @mixin \Eloquent
 */
trait Quietlable
{
    /**
     * Save the model without firing any model events
     *
     * @param array $options
     *
     * @return mixed
     */
    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    /**
     * Update the model without firing any model events
     *
     * @param array $attributes
     * @param array $options
     *
     * @return mixed
     */
    public function updateQuietly(array $attributes = [], array $options = [])
    {
        return static::withoutEvents(function () use ($attributes, $options) {
            return $this->update($attributes, $options);
        });
    }
}
