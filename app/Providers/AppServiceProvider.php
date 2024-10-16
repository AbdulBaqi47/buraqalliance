<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # Customer heading formatter for laravel-excel
        HeadingRowFormatter::extend('custom', function($value, $key) {
            return strtolower( Str::slug( preg_replace('/\s+/', '', $value) ) );
        });

        /*
        | ----------------------------------------------------
        |   Service Injection.
        | ----------------------------------------------------
        |
        */
        view()->share([
            'helper_service' => app()->make(\App\Services\InjectService::class),
        ]);

        $this->app->singleton('helper_service', function ($app) {
            return new \App\Services\InjectService();
        });

        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception

            $data = [
                'Job' => $event->job->resolveName(),
                'Payload' => $event->job->getRawBody(),
                'Trace' => $event->exception->getTraceAsString()
            ];

            Log::channel('slack')->critical("CRON FAIELD: ERROR => ".$event->exception->getMessage(), $data);
        });


        # ------------------------
        # Google Drive Filesystem
        # ------------------------
        // try {
            Storage::extend('google', function($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        // } catch(\Exception $e) {
        //     // your exception handling logic
        // }

        $migrationsPath = database_path('migrations');
        $directories    = glob($migrationsPath.'/central', GLOB_ONLYDIR);
        $paths          = array_merge([$migrationsPath], $directories);

        $this->loadMigrationsFrom($paths);
    }
}
