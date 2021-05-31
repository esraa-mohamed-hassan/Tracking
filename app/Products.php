<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Products extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name', 'sku', 'price', 'description', 'currency', 'url', 'brand_value', 'last_update', 'local_path', 'queue_id',
        'size', 'color', 'category', 'concentration', 'price_after_discount', 'texture', 'skin_type', 'area_of_apply', 'status', 'last_update_status', 'tags'

    ];

    static public function SaveData($name, $brand_value, $url, $currency, $description, $sku, $price, $local_path, $queue_id,
                                    $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply, $tags)
    {
        try {
            $item = new Products();
            $item->name = $name;
            $item->brand_value = $brand_value;
            $item->url = $url;
            $item->currency = $currency;
            $item->description = $description;
            $item->sku = $sku;
            $item->price = $price;
            $item->local_path = $local_path;
            $item->last_update = date("Y-m-d H:i:s");
            $item->queue_id = $queue_id;
            $item->price_after_discount = $price_after_discount;
            $item->category = $category;
            $item->size = $size;
            $item->color = $color;
            $item->concentration = $concentration;
            $item->texture = $texture;
            $item->skin_type = $skin_type;
            $item->area_of_apply = $area_of_apply;
            $item->tags = $tags;
            $item->save();
//            $log = new ScrappingLogs();
//            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'saved results from GoldenScent in products table', var_export($item, true), 'Error');
        } catch (\Exception $e) {

            ImportProducts::where('id', $queue_id)->update(['status' => 'failed']);
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveData Error with $queue_id = ' . $queue_id, var_export($e->getMessage(), true), 'Error');
        }
    }

    static public function UpdateData($name, $brand_value, $url, $currency, $description, $sku, $price, $local_path, $queue_id,
                                      $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply, $tags)
    {
        try {
            $data = [
                'name' => $name,
                'brand_value' => $brand_value,
                'url' => $url,
                'currency' => $currency,
                'description' => $description,
                'sku' => $sku,
                'price' => $price,
                'local_path' => $local_path,
                'last_update' => date("Y-m-d H:i:s"),
                'price_after_discount' => $price_after_discount,
                'category' => $category,
                'size' => $size,
                'color' => $color,
                'concentration' => $concentration,
                'texture' => $texture,
                'skin_type' => $skin_type,
                'area_of_apply' => $area_of_apply,
                'tags' => $tags,

            ];

            $update_pro = DB::table('products')
                ->where('sku', '=', $sku)
                ->update($data);
//            $log = new ScrappingLogs();
//            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'updated results from GoldenScent in products table', var_export($data, true), 'Error');
        } catch (\Exception $e) {

            ImportProducts::where('id', $queue_id)->update(['status' => 'failed']);
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'update data Error with $queue_id =' . $queue_id, var_export($e->getMessage(), true), 'Error');
        }
    }


    static public function GetAllProductsForGoldenScent($result)
    {
        try {

            $items = [];
            if (!empty($result['results'])) {
                foreach ($result['results'] as $res) {
                    $sku = $res['query'];

                    if (!empty($res['hits'])) {
                        $ds = DIRECTORY_SEPARATOR;
                        $ts = time();
                        $path = 'Scrapping' . $ds . $sku . $ds . date('Y-m-d', $ts) . $ds;
                        $file = $path . date('Y-m-d-His', $ts) . '-result-' . $sku . '.json';
//                        Storage::disk('local')->put($file, json_encode($res));

                        foreach ($res['hits'] as $itemb) {
                            $category = '';
                            $size = '';
                            $concentration = '';
                            $color = '';
                            $texture = '';
                            $skin_type = '';
                            $area_of_apply = '';
                            $tags = '';
                            $all_tags = [];

                            $local_path = $file;


                            $import_pro = ImportProducts::where('Sku', $sku)->selectRaw('id')->first();
                            $queue_id = $import_pro->id;
                            var_dump($queue_id);
                            if (count($itemb['sku']) > 1) {
                                var_dump('count sku greater than 1');
                                $associated_products = $itemb['associated_products'];
                                foreach ($associated_products as $ass_ptoduct) {
                                    $item_sku = $ass_ptoduct['associated_sku'];
                                    $products = Products::where('sku', $item_sku)->get();
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

                                    if($sku == $item_sku){
                                        if (array_key_exists("tags", $itemb)) {
                                            foreach ($itemb['tags'] as $tag){
                                                array_push($all_tags, $tag['name']);
                                            }
                                        }
                                        $tags = implode (", ", $all_tags);
                                    }

                                    if (count($products) == 0) {
                                        $name = $itemb['name'];
                                        $brand_value = isset($itemb['brand_value']) ? $itemb['brand_value'] : '';
                                        $url = $ass_ptoduct['url'];
                                        $currency = $itemb['currency'];
                                        $description = $itemb['description'];
                                        $price = $ass_ptoduct['regular_price'];
                                        $price_after_discount = $ass_ptoduct['special_price'];

                                        var_dump('add');
//                                            dd($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path, $queue_id,
//                                                $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply);

                                        Products::SaveData($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path, $queue_id,
                                            $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply, $tags);

                                    } else {
                                        $name = $itemb['name'];
                                        $brand_value = isset($itemb['brand_value']) ? $itemb['brand_value'] : '';
                                        $url = $ass_ptoduct['url'];
                                        $currency = $itemb['currency'];
                                        $description = $itemb['description'];
                                        $price = $ass_ptoduct['regular_price'];
                                        $price_after_discount = $ass_ptoduct['special_price'];

                                        var_dump('update');
//                                            dd($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path,$queue_id,
//                                                $price_after_discount, $category, $size, $color, $concentration,$texture, $skin_type, $area_of_apply);

                                        Products::UpdateData($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path, $queue_id,
                                            $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply, $tags);

                                    }
                                }
                            } else {
                                if (!isset($itemb['associated_products'])) {
                                    var_dump('count sku is 1');

                                    if (array_key_exists("product_type_new2", $itemb)) {
                                        $category = $itemb['product_type_new2'];
                                    }
                                    if (array_key_exists("size_new", $itemb)) {
                                        $size = $itemb['size_new'];
                                    }
                                    if (array_key_exists("color", $itemb)) {
                                        $color = $itemb['color'][0];
                                    }
                                    if (array_key_exists("concentration", $itemb)) {
                                        $concentration = $itemb['concentration'];
                                    }
                                    if (array_key_exists("texture", $itemb)) {
                                        $texture = $itemb['texture'];
                                    }
                                    if (array_key_exists("skin_type", $itemb)) {
                                        $skin_type = $itemb['skin_type'];
                                    }
                                    if (array_key_exists("area_of_apply", $itemb)) {
                                        $area_of_apply = $itemb['area_of_apply'];
                                    }

                                    $item_sku = $itemb['sku'][0];
                                    if($sku == $item_sku){
                                        if (array_key_exists("tags", $itemb)) {
                                            foreach ($itemb['tags'] as $tag){
                                                array_push($all_tags, $tag['name']);
                                            }
                                        }
                                        $tags = implode (", ", $all_tags);
                                    }
                                    $products = Products::where('sku', $item_sku)->get();
                                    if (count($products) == 0) {
                                        $name = $itemb['name'];
                                        $brand_value = isset($itemb['brand_value']) ? $itemb['brand_value'] : '';
                                        $url = $itemb['url'];
                                        $currency = $itemb['currency'];
                                        $description = $itemb['description'];
                                        $price = $itemb['price']['SAR']['default_original'];
                                        $price_after_discount = $itemb['price']['SAR']['default_original'];

                                        var_dump('add');
//                                            dd($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path, $queue_id,
//                                                $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply);

                                        Products::SaveData($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path, $queue_id,
                                            $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply, $tags);

                                    } else {

                                        $name = $itemb['name'];
                                        $brand_value = isset($itemb['brand_value']) ? $itemb['brand_value'] : '';
                                        $url = $itemb['url'];
                                        $currency = $itemb['currency'];
                                        $description = $itemb['description'];
                                        $price = $itemb['price']['SAR']['default_original'];
                                        $price_after_discount = $itemb['price']['SAR']['default'];

                                        var_dump('update');
//                                            dd($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path,$queue_id,
//                                                $price_after_discount, $category, $size, $color, $concentration,$texture, $skin_type, $area_of_apply);

                                        Products::UpdateData($name, $brand_value, $url, $currency, $description, $item_sku, $price, $local_path, $queue_id,
                                            $price_after_discount, $category, $size, $color, $concentration, $texture, $skin_type, $area_of_apply, $tags);

                                    }
                                }
                            }

                        }
                        ImportProducts::where('Sku', $sku)->update(['status' => 'done']);

                    } else {
                        $log = new ScrappingLogs();
                        $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'saved results from GoldenScent in products table if product not found', var_export(array(), true), 'Error');
                        ImportProducts::where('Sku', $sku)->update(['status' => 'not-found']);
                    }
                }
            }


        } catch (\Exception $e) {

            ImportProducts::where('Sku', $sku)->update(['status' => 'failed']);
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Scrapping products from GoldenScent Error', var_export($e->getMessage(), true), 'Error');
        }
    }
}
