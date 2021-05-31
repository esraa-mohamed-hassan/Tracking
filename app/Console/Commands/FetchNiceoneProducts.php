<?php

namespace App\Console\Commands;

use App\ImportProducts;
use App\NiceoneProducts;
use App\ScrappingLogs;
use App\Products;
use App\UpdateGoldenScentPricesForProducts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FetchNiceoneProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:niceone_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch products from Niceone site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */


    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        try {
            $categories = NiceoneProducts::GetAllCategories();
            if(!empty($categories)){
                $products = NiceoneProducts::GetAllProductsByCategoryId($categories);

            }else{
                $log = new Logs();
                $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, '$categories is empty', var_export($categories, true), 'Error');
            }
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Scrapping products from GoldenScent Error', var_export($e->getMessage(), true), 'Error');
        }
    }
}
