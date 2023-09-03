<?php

namespace NuclearAnt\LaravelSnowflake;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Facades\Snowflake;

trait HasSnowflake
{
    public static function bootHasSnowflake(): void
    {
        static::created(static function (/** @var static $model */ Model $model) {
            if ($model->getKeyType() !== 'int') {
                return;
            }

            $model->updateQuietly([
                $model->getSnowflakeKeyName() => $model->getSnowflakeKey() ?? Snowflake::encode($model->getKey()),
            ]);
        });
    }

    /**
     * Get the snowflake key for the model.
     */
    public function getSnowflakeKeyName(): string
    {
        return 'sid';
    }

    /**
     * Get the value of the model's snowflake key.
     */
    public function getSnowflakeKey(): ?int
    {
        return $this->getAttribute($this->getSnowflakeKeyName());
    }
}
