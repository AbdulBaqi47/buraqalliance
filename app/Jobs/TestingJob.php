<?php

# ---------------------------------------------------
# CMD: php artisan tinker -> \App\Jobs\TestingJob::dispatchSync();
# LOGS PATH: /var/www/limo.buraqalliance.com/storage/logs/crons/test-{YYYY-MM-DD}.log
# ---------------------------------------------------



namespace App\Jobs;


use App\Helpers\Suppliers;
use App\Http\Middleware\HttpMacros;
use App\Models\Tenant\Brand;
use App\Models\Tenant\Collection;
use App\Models\Tenant\PriceModifier;
use App\Models\Tenant\Product;
use App\Models\Tenant\Shop;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\Variant;
use App\Models\Tenant\VariantInventory;
use App\Models\Tenant\Warehouse;
use App\Models\Tenant\WorkerProgress;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class TestingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 86400;


    /**
     * Create a new job instance.
     *
     * @param  array $products
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);

        Log::setDefaultDriver('test-cron'); // Set Default Driver

        Log::info("------------------------ [Testing Job - TESTING ( START )] ------------------------");

        for ($i=0; $i < 3600; $i++) {
            sleep(1);

            Log::info('INDEX = '.$i);

            // if($i === 50){
            //     throw new Exception("Some error occured");
            // }
        }

        Log::info("------------------------ [Testing Job - TESTING ( END )] ------------------------");
    }

}
