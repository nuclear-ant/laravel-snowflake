<?php

namespace NuclearAnt\LaravelSnowflake\Commands;

use Brick\VarExporter\VarExporter;
use Illuminate\Console\Command;
use Jenssegers\Optimus\Energon;
use Jenssegers\Optimus\Exceptions\InvalidPrimeException;

class SparkCommand extends Command
{
    protected $signature = 'snowflake:generate {prime} {--bits=31}';

    protected $description = 'Generate constructor values for Optimus prime';

    public function handle(): int
    {
        $bitLength = $this->option('bits');

        $minBitLength = 4;
        $maxBitLength = 62;

        if (!filter_var(
            $bitLength,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => $minBitLength, 'max_range' => $maxBitLength]]
        )) {
            $this->error("The bits option must be an integer between $minBitLength and $maxBitLength.");

            return static::FAILURE;
        }

        try {
            [$prime, $inverse, $random] = Energon::generate($this->argument('prime'), $bitLength);
        } catch (InvalidPrimeException) {
            $this->error('Invalid prime number.');

            return static::FAILURE;
        }

        $config = [
            'prime' => $prime,
            'inverse' => $inverse,
            'random' => $random,
            'size' => $bitLength,
        ];

        file_put_contents(config_path('snowflake.php'), sprintf("<?php \n\n return %s", VarExporter::export($config)));

        return static::SUCCESS;
    }
}
