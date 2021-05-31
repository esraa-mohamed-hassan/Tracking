<?php

namespace App\Console\Commands;

use App\DorephaProducts;
use App\ScrappingLogs;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Illuminate\Console\Command;

class FetchDorephaProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:dorepha_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all products from Dorepha site';

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
        try {
            DorephaProducts::query()->truncate();
            $count_pages = DorephaProducts::CountAllProductsDorepha();
            for ($i = 1; $i <= $count_pages; $i++) {
                try {
                    $end_point = 'products';
                    $params = [
                        'page' => $i,
                        'per_page' => 100,
                    ];
                    $result = DorephaProducts::RequestDorepha($end_point, $params);
                    var_dump($i);
                    if (!empty($result)) {
                        foreach ($result as $res) {
                            $product_id = $res->id;
                            $name = $res->name;
                            $slug = $res->slug;
                            $sku = $res->sku;
                            $price = $res->regular_price;
                            $price_after_discount = $res->sale_price;
                            $description = !empty($res->description) ? $res->description : $res->short_description;
                            $pro_type = $res->type;
                            $pro_status = $res->status;
                            $stock_quantity = $res->stock_quantity;
                            $stock_status = $res->stock_status;
                            $url = $res->permalink;
                            $categorie = !empty($res->categories) ? $res->categories[0]->name : $res->categories;

                            $product = DorephaProducts::where('sku', $sku)->get();
                            if (count($product) == 0) {
                                var_dump('added');
                                DorephaProducts::SaveDataPro($product_id, $name, $slug, $sku, $price, $price_after_discount, $categorie, $description, $pro_type, $pro_status, $stock_quantity, $stock_status, $url);
                            } else {
                                var_dump('update');
                                DorephaProducts::UpdateDataPro($product_id, $name, $slug, $sku, $price, $price_after_discount, $categorie, $description, $pro_type, $pro_status, $stock_quantity, $stock_status, $url);
                            }
                        }
                    }
                } catch (HttpClientException $e) {
                    $data = "\n Exception Caught" . $e->getMessage();
                    $log = new ScrappingLogs();
                    $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Fetch all products from Dorepha Error', var_export($data, true), 'Error');
                };
            }
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Fetch all products from Dorepha Error', var_export($e->getMessage(), true), 'Error');
        }

    }
}
