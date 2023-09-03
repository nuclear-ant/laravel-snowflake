<?php

namespace NuclearAnt\LaravelSnowflake;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Jenssegers\Optimus\Optimus;
use NuclearAnt\LaravelSnowflake\Commands\SparkCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SnowflakeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-snowflake')
            ->hasConfigFile()
            ->hasInstallCommand(fn (InstallCommand $command) => $command
                ->publishConfigFile()
                ->endWith(fn (InstallCommand $command) => $command
                    ->call(SparkCommand::class)
                )
            );
    }

    /**
     * @throws InvalidPackage
     */
    public function register(): void
    {
        parent::register();

        Blueprint::macro('snowflake', fn (string $column = 'sid'): ColumnDefinition => $this->unsignedBigInteger($column)->nullable()->index());

        $this->app->singleton('optimus', function (Application $app) {
            $config = $app['config']->get('snowflake');

            return new Optimus(
                prime: $config['prime'],
                inverse: $config['inverse'],
                xor: $config['random'],
                size: $config['size'],
            );
        });
    }
}
