<?php

namespace App\Console\Commands;

use App\ImportProducts;
use App\ScrappingLogs;
use App\Products;
use App\UpdateGoldenScentPricesForProducts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchGoldenProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch products from golden scent site';

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
            ImportProducts::selectRaw('id, Sku')->where('status', 'pending')->orderBy('id')->chunk(50, function ($ptoducts) use (&$all_data) {
                $index_name = 'magento_ar_sa_products';
                $data = [];
                foreach ($ptoducts as $i => $product) {
                    if ($i % 50 == 0) {
                        var_dump('sleep');
                        sleep(rand(0, 2));
                    }
                    $queries = [
                        'indexName' => $index_name,
                        'query' => $product->Sku,
                        'typoTolerance' => true,
                        'hitsPerPage' => 500
                    ];
                    array_push($data, $queries);
                }

                try {
                    $result = UpdateGoldenScentPricesForProducts::RequestGoldenScent($data);
                    $update_prices = Products::GetAllProductsForGoldenScent($result);
                    $all_data[] = $update_prices;
                } catch (\Exception $e) {
                    echo 'error';
                    $log = new ScrappingLogs();
                    $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error ', var_export($e->getMessage(), true), 'Error');
                }
            });
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Scrapping products from GoldenScent Error', var_export($e->getMessage(), true), 'Error');
        }
    }
}
