<?php

namespace Spatie\LaravelPackageTools;

use Illuminate\Contracts\Foundation\Application;
use Jenssegers\Optimus\Commands\SparkCommand;
use Jenssegers\Optimus\Optimus;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;

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

        $this->app->singleton('optimus', function (Application $app) {
            $config = $app['config']['snowflake'];

            return new Optimus(
                prime: $config['prime'],
                inverse: $config['inverse'],
                xor: $config['xor'],
                size: $config['size'],
            );
        });
    }
}
