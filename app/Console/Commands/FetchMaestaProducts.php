<?php

namespace App\Console\Commands;

use App\MaestaSize;
use App\MaestaBrand;
use App\MaestaCategory;
use App\MaestaProducts;
use App\MaestaSkinType;
use App\ScrappingLogs;
use Illuminate\Console\Command;

class FetchMaestaProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:maesta_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all products from Maesta site';

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
            MaestaProducts::query()->truncate();
            $count_pages = MaestaProducts::CountAllProductsMaesta();

            if (env('APP_ENV') == 'local') {
                $env_url = env("Magento_URL_LIVE");
            } else {
                $env_url = env("Magento_URL");
            }
            for ($i = 1; $i <= $count_pages; $i++) {
                var_dump($i);

                $url = $env_url . '/rest/V1/products';
                $params = [
                    'searchCriteria[currentPage]' => $i,
                    'searchCriteria[pageSize]' => '500',
                ];
                $response = MaestaProducts::RequestMaesta($url, $params);
                if (!empty($response)) {
                    $result = $response['items'];
                    if (!empty($result)) {
                        foreach ($result as $res) {
                            $price_after_discount = 0;
                            $product_id = $res['id'];
                            $name = $res['name'];
                            $sku = $res['sku'];
                            $price = $res['price'];
                            $pro_type = $res['type_id'];
                            $pro_status = $res['status'];
                            $visibility = $res['visibility'];
                            $brand_name = '';
                            $category_name = '';
                            $size_name = '';
                            $skin_type_name = '';
                            $custom_attributes = $res['custom_attributes'];
                            foreach ($custom_attributes as $attr) {
                                if ($attr['attribute_code'] == 'description') {
                                    $description = $attr['value'];
                                }

                                if ($attr['attribute_code'] == 'special_price') {
                                    $price_after_discount = $attr['value'];
                                }

                                if ($attr['attribute_code'] == 'url_key') {
                                    $url = env("Magento_URL_LIVE") . '/' . $attr['value'] . '.html';
                                }

                                if ($attr['attribute_code'] == 'manufacturer') {
                                    $brand = MaestaBrand::Where('brand_id', $attr['value'])->first();
                                    if(!empty($brand)){
                                        $brand_name = $brand['name'];
                                    }
                                }

                                if ($attr['attribute_code'] == 'size') {
                                    $size = MaestaSize::Where('size_id', $attr['value'])->first();
                                    if(!empty($size)){
                                        $size_name = $size['name'];
                                    }
                                }

                                if ($attr['attribute_code'] == 'make_up') {
                                    $skin_type = MaestaSkinType::Where('skin_type_id', $attr['value'])->first();
                                    if(!empty($skin_type)){
                                        $skin_type_name = $skin_type['name'];
                                    }
                                }

                                if ($attr['attribute_code'] == 'category_ids') {
                                    foreach ($attr['value'] as $val) {
                                        $category = MaestaCategory::Where('category_id', $val)->first();
                                        if(!empty($category)){
                                            $category_name = $category['name'];
                                            break;
                                        }
                                    }
                                }
                            }
                            $product = MaestaProducts::where('sku', $sku)->get();
                            if (count($product) == 0) {
                                var_dump('added');
                                MaestaProducts::SaveDataPro($product_id, $name, $sku, $price, $price_after_discount, $description, $pro_type, $pro_status, $visibility, $url,
                                    $brand_name, $category_name, $size_name, $skin_type_name);
                            } else {
                                var_dump('update');
                                MaestaProducts::UpdateDataPro($product_id, $name, $sku, $price, $price_after_discount, $description, $pro_type, $pro_status, $visibility, $url,
                                    $brand_name, $category_name, $size_name, $skin_type_name);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Fetch all products from Maesta Error', var_export($e->getMessage(), true), 'Error');
        }

    }
}
