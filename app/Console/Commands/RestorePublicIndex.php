<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RestorePublicIndex extends Command
{
    protected $signature = 'app:restore-public-index';

    protected $description = 'Restore the missing public/index.php entry point';

    public function handle(): int
    {
        $path = public_path('index.php');

        if (file_exists($path)) {
            $this->components->info('public/index.php already exists.');

            return self::SUCCESS;
        }

        $contents = <<<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP;

        if (@file_put_contents($path, $contents) === false) {
            $this->components->error('Could not write public/index.php (permission denied).');
            $this->line('Create the file manually with this path:');
            $this->line($path);
            $this->newLine();
            $this->line('Until then, php artisan serve uses storage/laravel-serve-router.php.');

            return self::FAILURE;
        }

        $this->components->info('Restored public/index.php successfully.');

        return self::SUCCESS;
    }
}
