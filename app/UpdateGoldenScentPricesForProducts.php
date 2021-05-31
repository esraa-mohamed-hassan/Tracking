<?php

namespace App;

use App\Products;
use App\ScrappingLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UpdateGoldenScentPricesForProducts extends Model
{
    protected $table = 'update_goldenscent_price';
    protected $fillable = [
        'sku', 'price', 'price_after_discount', 'last_update'
    ];

    static public function GetProductsFromTable()
    {
        while (true) {
            try {
                $products = Products::where('status', 'pending')->where('last_update_status', 'like', date("Y-m-d") . '%')->orderBy('id')->get();
                if (count($products) == 0) {
                    $i = 0;
                    if ($i == 0) {
                        echo 'count zero';
                        Products::query()->update([
                            'status' => 'pending',
                            'last_update_status' => date("Y-m-d H:i:s"),
                        ]);
                        $all_data[] = UpdateGoldenScentPricesForProducts::GetProductsToUpdate();
                        $i++;
                        echo '$i is :' . $i;
                    } else {
                        echo '$i is greater than zero';
                        break;
                    }
                } else {
                    var_dump('count:' . count($products));
                    $all_data[] = UpdateGoldenScentPricesForProducts::GetProductsToUpdate();
                }
            } catch (\Exception $e) {
                echo 'error';
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error ', var_export($e->getMessage(), true), 'Error');
            }
            break;
        }
    }

    static public function GetProductsToUpdate()
    {
        try {
            Products::orderBy('id')->where('status', 'pending')->chunk(50, function ($ptoducts) use (&$all_data) {
                $index_name = 'magento_ar_sa_products';
                $data = [];
                foreach ($ptoducts as $i => $product) {
                    if ($i % 50 == 0) {
                        var_dump('sleep');
                        $time_sleep = sleep(rand(0, 2));
                    }
                    $queries = [
                        'indexName' => $index_name,
                        'query' => $product->sku,
                        'typoTolerance' => true,
                        'hitsPerPage' => 500
                    ];
                    array_push($data, $queries);
                }
                try {

                    $result = UpdateGoldenScentPricesForProducts::RequestGoldenScent($data);
                    $update_prices = UpdateGoldenScentPricesForProducts::UpdatePricesForGoldenScent($result);
                    $all_data[] = $update_prices;
                } catch (\Exception $e) {
                    echo 'error';
                    $log = new ScrappingLogs();
                    $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error ', var_export($e->getMessage(), true), 'Error');
                }
            });
            return $all_data;
        } catch (\Exception $e) {
            echo 'error';
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    static public function RequestGoldenScent($queries)
    {
        try {

            $app_id = env('ALGOLIA_APP_ID');
            $app_key = env('ALGOLIA_SECRET');

            $config = \Algolia\AlgoliaSearch\Config\SearchConfig::create($app_id, $app_key);
            $config->setConnectTimeout(500);
            $config->setReadTimeout(500);
            $client = \Algolia\AlgoliaSearch\SearchClient::createWithConfig($config);

            $res = $client->multipleQueries($queries);
            return $res;
        } catch (\Exception $e) {
            echo $e->getMessage();
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'RequestGoldenScents Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    static public function UpdatePricesForGoldenScent($result)
    {
        try {
            $items = [];
            if (!empty($result['results'])) {
                foreach ($result['results'] as $res) {

                    $sku = $res['query'];
                    $ds = DIRECTORY_SEPARATOR;
                    $ts = time();
                    $path = 'Scrapping_update_price' . $ds . date('Y-m-d', $ts) . $ds;
                    $file = $path . date('Y-m-d-His', $ts) . '-result-' . $sku . '.json';
                    //                    Storage::disk('local')->put($file, json_encode($result));

                    if (!empty($res['hits'])) {
                        foreach ($res['hits'] as $hit) {
                            $category = '';
                            $size = '';
                            $concentration = '';
                            $color = '';
                            $texture = '';
                            $skin_type = '';
                            $area_of_apply = '';
                            $tags = '';
                            $all_tags = [];

                            if (count($hit['sku']) > 1) {
                                var_dump('count sku greater than 1');
                                $associated_products = $hit['associated_products'];
                                foreach ($associated_products as $ass_ptoduct) {

                                    $item_sku = $ass_ptoduct['associated_sku'];
                                    $price = $ass_ptoduct['regular_price'];
                                    $price_after_discount = $ass_ptoduct['special_price'];

                                    /* Start Check if product is not find in products table and add it*/
                                    $products = Products::where('sku', $item_sku)->orderBy('id')->get();
                                    echo $item_sku . '<br>';

                                    $product_details = $ass_ptoduct['product_details'];
                                    if (count($product_details) > 0) {
                                        if (array_key_exists("product_type_new2", $product_details)) {
                                            $category = $product_details['product_type_new2']['value'];
                                        }

                                        if (array_key_exists("size_new", $product_details)) {
                                            $size = $product_details['size_new']['value'];
                                        }

                                        if (array_key_exists("color", $product_details)) {
                                            $color = $product_details['color']['value'];
                                        }

                                        if (array_key_exists("concentration", $product_details)) {
                                            $concentration = $product_details['concentration']['value'];
                                        }

                                        if (array_key_exists("texture", $product_details)) {
                                            $texture = $product_details['texture']['value'];
                                        }

                                        if (array_key_exists("skin_type", $product_details)) {
                                            $skin_type = $product_details['skin_type']['value'];
                                        }

                                        if (array_key_exists("area_of_apply", $product_details)) {
                                            $area_of_apply = $product_details['area_of_apply']['value'];
                                        }
                                    }
                                    if ($sku == $item_sku) {
                                        if (array_key_exists("tags", $hit)) {
                                            foreach ($hit['tags'] as $tag) {
                                                array_push($all_tags, $tag['name']);
                                            }
                                        }
                                        $tags = implode(", ", $all_tags);
                                    }
                                    if (count($products) == 0) {
                                        //                                            echo 'Not Found greater products table';
                                        $name = $hit['name'];
                                        $brand_value = isset($hit['brand_value']) ? $hit['brand_value'] : '';
                                        $url = $ass_ptoduct['url'];
                                        $currency = $hit['currency'];
                                        $description = $hit['description'];

                                        Products::SaveData(
                                            $name,
                                            $brand_value,
                                            $url,
                                            $currency,
                                            $description,
                                            $item_sku,
                                            $price,
                                            $file,
                                            '',
                                            $price_after_discount,
                                            $category,
                                            $size,
                                            $color,
                                            $concentration,
                                            $texture,
                                            $skin_type,
                                            $area_of_apply,
                                            $tags
                                        );
                                    } else {
                                        //                                            echo 'found greater products table';
                                        Products::where('sku', $sku)->update([
                                            'status' => 'done',
                                            'last_update_status' => date("Y-m-d H:i:s"),
                                        ]);
                                    }
                                    /* End Check if product is not find in products table and add it*/


                                    /* Start add sku and prices in update_goldenscent_price table */
                                    $product = UpdateGoldenScentPricesForProducts::where('sku', $item_sku)->where('last_update', 'like', date("Y-m-d") . '%')->get();
                                    if (count($product) == 0) {
                                        UpdateGoldenScentPricesForProducts::SavePricesAllProducts($item_sku, $price, $price_after_discount);
                                    } else {
                                        echo 'sku greater added before in DB';
                                    }
                                    /* End add sku and prices in update_goldenscent_price table*/
                                }
                            } else {
                                if (!isset($hit['associated_products'])) {
                                    //                                    var_dump('count sku is 1');
                                    $item_sku = $hit['sku'][0];
                                    $price = $hit['price']['SAR']['default_original'];
                                    $price_after_discount = $hit['price']['SAR']['default'];
                                    if ($sku == $item_sku) {
                                        if (array_key_exists("tags", $hit)) {
                                            foreach ($hit['tags'] as $tag) {
                                                array_push($all_tags, $tag['name']);
                                            }
                                        }
                                        $tags = implode(", ", $all_tags);
                                    }

                                    /* Start Check if product is not find in products table and add it*/
                                    if (array_key_exists("product_type_new2", $hit)) {
                                        $category = $hit['product_type_new2'];
                                    }
                                    if (array_key_exists("size_new", $hit)) {
                                        $size = $hit['size_new'];
                                    }
                                    if (array_key_exists("color", $hit)) {
                                        $color = $hit['color'][0];
                                    }
                                    if (array_key_exists("concentration", $hit)) {
                                        $concentration = $hit['concentration'];
                                    }
                                    if (array_key_exists("texture", $hit)) {
                                        $texture = $hit['texture'];
                                    }
                                    if (array_key_exists("skin_type", $hit)) {
                                        $skin_type = $hit['skin_type'];
                                    }
                                    if (array_key_exists("area_of_apply", $hit)) {
                                        $area_of_apply = $hit['area_of_apply'];
                                    }

                                    $products = Products::where('sku', $item_sku)->get();
                                    if (count($products) == 0) {
                                        //                                            echo 'Not  Found products table';
                                        $name = $hit['name'];
                                        $brand_value = isset($hit['brand_value']) ? $hit['brand_value'] : '';
                                        $url = $hit['url'];
                                        $currency = $hit['currency'];
                                        $description = $hit['description'];

                                        Products::SaveData(
                                            $name,
                                            $brand_value,
                                            $url,
                                            $currency,
                                            $description,
                                            $item_sku,
                                            $price,
                                            $file,
                                            '',
                                            $price_after_discount,
                                            $category,
                                            $size,
                                            $color,
                                            $concentration,
                                            $texture,
                                            $skin_type,
                                            $area_of_apply,
                                            $tags
                                        );
                                    } else {
                                        //                                            echo ' found products table';
                                        Products::where('sku', $sku)->update([
                                            'status' => 'done',
                                            'last_update_status' => date("Y-m-d H:i:s"),
                                        ]);
                                    }
                                    /* End Check if product is not find in products table and add it*/

                                    /* Start add sku and prices in update_goldenscent_price table */
                                    $product = UpdateGoldenScentPricesForProducts::where('sku', $item_sku)->where('last_update', 'like', date("Y-m-d") . '%')->get();
                                    if (count($product) == 0) {
                                        UpdateGoldenScentPricesForProducts::SavePricesAllProducts($item_sku, $price, $price_after_discount);
                                    } else {
                                        echo 'sku added before in DB';
                                    }
                                    /* End add sku and prices in update_goldenscent_price table*/
                                }
                            }
                        }
                    } else {
                        Products::where('sku', $res['query'])->update(['status' => 'not-found']);
                        $log = new ScrappingLogs();
                        $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error', var_export('No Data in hits', true), 'Error');
                    }
                }
            } else {
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error', var_export('No Data in results', true), 'Error');
            }
            return $items;
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForGoldenScent Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    static public function SavePricesAllProducts($sku, $price, $price_after_discount)
    {
        try {
            var_dump($sku);
            $item = new UpdateGoldenScentPricesForProducts();
            $item->sku = $sku;
            $item->price = $price;
            $item->price_after_discount = $price_after_discount;
            $item->last_update = date("Y-m-d H:i:s");
            $item->setUpdatedAt(null);
            $item->save();

            var_dump('saved');

            Products::where('sku', $sku)->update([
                'status' => 'done',
                'last_update_status' => date("Y-m-d H:i:s"),
            ]);
            return $item;
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SavePricesAllProducts GoldenScent Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
