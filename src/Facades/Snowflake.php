<?php

namespace Spatie\LaravelPackageTools\Facades;

use Illuminate\Support\Facades\Facade;
use Jenssegers\Optimus\Optimus;

/**
 * @method static int encode(int $value)
 *
 * @see Optimus
 */
class Snowflake extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'optimus';
    }
}