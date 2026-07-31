<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DevServe extends Command
{
    protected $signature = 'serve
                            {--host=127.0.0.1 : The host address to serve the application on}
                            {--port=8000 : The port to serve the application on}
                            {--no-reload : Unused. Kept for compatibility with Laravel\'s serve command}';

    protected $description = 'Serve the application on the PHP development server';

    public function handle(): int
    {
        $host = (string) $this->option('host');
        $port = (int) $this->option('port');

        if (is_file(public_path('index.php'))) {
            $this->components->info("Server running on [http://{$host}:{$port}].");
            $this->comment('Press Ctrl+C to stop the server');
            $this->newLine();

            $command = sprintf(
                '"%s" -d display_startup_errors=0 -S %s:%d -t "%s"',
                PHP_BINARY,
                $host,
                $port,
                public_path()
            );

            passthru($command, $exitCode);

            return (int) $exitCode;
        }

        $router = $this->routerPath();

        $this->components->info("Server running on [http://{$host}:{$port}].");
        $this->comment('Press Ctrl+C to stop the server');
        $this->newLine();

        $previousDirectory = getcwd();
        chdir(public_path());

        $command = sprintf(
            '"%s" -d display_startup_errors=0 -S %s:%d "%s"',
            PHP_BINARY,
            $host,
            $port,
            $router
        );

        passthru($command, $exitCode);

        if (is_string($previousDirectory) && $previousDirectory !== '') {
            chdir($previousDirectory);
        }

        return (int) $exitCode;
    }

    protected function routerPath(): string
    {
        $candidates = [
            __DIR__.DIRECTORY_SEPARATOR.'serve-router.php',
            base_path('app'.DIRECTORY_SEPARATOR.'Console'.DIRECTORY_SEPARATOR.'Commands'.DIRECTORY_SEPARATOR.'serve-router.php'),
            rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'job-ad-serve-router.php',
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        $contents = $this->routerScript();

        foreach ($candidates as $path) {
            $directory = dirname($path);

            if (! is_dir($directory)) {
                @mkdir($directory, 0777, true);
            }

            if (@file_put_contents($path, $contents) !== false) {
                return $path;
            }
        }

        throw new \RuntimeException(
            'Could not create the development server router. Run: php run-server.php or double-click serve.bat'
        );
    }

    protected function routerScript(): string
    {
        return <<<'PHP'
<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists($publicPath.$uri)) {
    return false;
}

define('LARAVEL_START', microtime(true));

$basePath = dirname($publicPath);

if (file_exists($maintenance = $basePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath.'/vendor/autoload.php';

$app = require_once $basePath.'/bootstrap/app.php';

$app->handleRequest(Illuminate\Http\Request::capture());
PHP;
    }
}
