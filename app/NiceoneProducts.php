<?php

namespace App;

use App\ScrappingLogs;
use App\NiceoneOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NiceoneProducts extends Model
{
    protected $table = 'niceone_products';
    protected $fillable = [
        'product_id', 'name_en', 'name_ar', 'sku', 'description_en', 'description_ar', 'price', 'price_after_discount', 'discount_ratio', 'currency', 'url_en', 'url_ar', 'brand_value', 'category',
        'concentration', 'size', 'color', 'texture', 'skin_type', 'area_of_apply', 'tags', 'pro_status', 'last_update', 'status',
        'stock_quantity',
    ];

    public static function RequestNiceone($url)
    {
        try {
            $response = Http::withHeaders([
                'x-oc-merchant-id' => env('NICEONE_X_OC_MERCHANT_ID'),
                'x-oc-restadmin-id' => env('NICEONE_X_OC_RESTADMIN_ID'),
            ])->timeout(60)->get($url);

            if ($response->status() == 200) {

                return $response->json();

            } else if ($response->serverError()) {
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'RequestNiceone serverError ', var_export($response->body(), true), 'Error');
                return $response->throw();
            }
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'RequestNiceone Error', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function GetAllCategories()
    {
        try {
            $response = NiceoneProducts::RequestNiceone('https://niceonesa.com/?route=rest/v2/app/getCategories');
            $res = $response['data']['categories'];
            $categories = [];
            foreach ($res as $cat) {
                array_push($categories, [
                    'id' => $cat['category_id'],
                    'name' => $cat['name']
                ]);
            }
            return $categories;

        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetAllCategories Niceone Error', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function GetDetailsProductById($id, $category)
    {
        try {
            $texture = '';
            $skin_type = '';
            $price_after_discount = 0;
            $tag_name = null;
            $ratio = 0;

            $url = 'https://niceonesa.com/?route=rest/product_admin/products&id=' . $id;
            $response = NiceoneProducts::RequestNiceone($url);

            if (!empty($response['data']['product_description'])) {
                foreach ($response['data']['product_description'] as $details) {
                    if ($details['language_id'] == "1") {
                        $name_en = $details['name'];
                        $description_en = $details['description'];
                        $url_en = 'https://niceonesa.com/en/' . $details['name'] . $response['data']['seo_url_en'];
                    }
                    if ($details['language_id'] == "2") {
                        $name_ar = $details['name'];
                        $description_ar = $details['name'];
                        $url_ar = 'https://niceonesa.com/ar/' . $details['name'] . $response['data']['seo_url_ar'];
                    }
                }
            }

            if (!empty($response['data']['special'])) {
                foreach ($response['data']['special'] as $details) {
                    if(isset($details['price_formated'])){
                        $price_after_discount = explode(" ريال ", $details['price_formated'])[0];
                    }
                    if(isset($details['tag_name'])){
                        $tag_name = $details['tag_name'];
                    }
                    if(isset($details['discount_ratio'])){
                        $ratio = $details['discount_ratio'];
                    }
                    break;
                }
            }

            if (!empty($response['data']['product_attributes'])) {
                $attributes = $response['data']['product_attributes']['attributes'];
                foreach ($attributes as $attr) {
                    switch ($attr['2']['attribute_group_id']) {
                        case '24':
                            $texture .= $attr['2']['name'] . ',';
                            break;
                        case '26':
                            $skin_type .= $attr['2']['name'] . ',';
                            break;
                    }
                }
            }

            $all_options = $response['data']['options'];


            $product_id = $response['data']['id'];
            $sku = $response['data']['isbn'];
            $price = explode(" ريال ", $response['data']['price_formated'])[0];
            $currency = explode(" ", $response['data']['price_formated'])[1];
            $brand_value = $response['data']['manufacturer'];
            $status = $response['data']['status'];
            $stock_quantity = $response['data']['quantity'];
            $tags = $tag_name;

            if (!empty($sku)) {
                $products = NiceoneProducts::Where('sku', $sku)->get();
                if (count($products) == 0) {
                    $response = NiceoneProducts::SaveData($product_id, $sku, $name_ar, $name_en, $description_en, $description_ar, $url_en,
                        $url_ar, $price, $price_after_discount, $ratio, $currency, $brand_value, $category, $status, $tags, $stock_quantity, $texture, $skin_type, $all_options);
                } else {
                    $response = NiceoneProducts::UpdateData($product_id, $sku, $name_ar, $name_en, $description_en, $description_ar, $url_en,
                        $url_ar, $price, $price_after_discount, $ratio, $currency, $brand_value, $category, $status, $tags, $stock_quantity, $texture, $skin_type, $all_options);
                }
            } else {
                $products = NiceoneProducts::Where('product_id', $product_id)->get();
                if (count($products) == 0) {
                    $new_sku = 'SCR_' . $response['data']['sku'];
                    $response = NiceoneProducts::SaveData($product_id, $new_sku, $name_ar, $name_en, $description_en, $description_ar, $url_en,
                        $url_ar, $price, $price_after_discount, $ratio, $currency, $brand_value, $category, $status, $tags, $stock_quantity, $texture, $skin_type, $all_options);
                } else {
                    $response = NiceoneProducts::UpdateData($product_id, $sku, $name_ar, $name_en, $description_en, $description_ar, $url_en,
                        $url_ar, $price, $price_after_discount, $ratio, $currency, $brand_value, $category, $status, $tags, $stock_quantity, $texture, $skin_type, $all_options);
                }
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Sku is Null product_id=' . $id, var_export($id, true), 'Error');

            }
            return $response;
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetDetailsProductById Niceone Error with product_id=' . $id, var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function GetAllProductsByCategoryId($categories)
    {
        try {
            foreach ($categories as $category) {
                sleep(rand(0, 2));
                $url = 'https://niceonesa.com?route=rest/product_admin/products&category=' . $category['id'] . '&limit=5000';
                $response = NiceoneProducts::RequestNiceone($url);
                foreach ($response['data']['products'] as $i => $pro) {
                    if ($i % 50 == 0) {
                        var_dump('sleep');
                        sleep(rand(0, 2));
                    }
                    $product = NiceoneProducts::GetDetailsProductById($pro['id'], $category['name']);
                }
            }
            return 'added all products successfully';

        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetAllProductsByCategoryId Niceone Error', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function SaveData($product_id, $sku, $name_ar, $name_en, $description_en, $description_ar, $url_en,
                                    $url_ar, $price, $price_after_discount, $ratio, $currency, $brand_value, $category,
                                    $status, $tags, $stock_quantity, $texture, $skin_type, $all_options)
    {
        try {
            echo 'add';
            $item = new NiceoneProducts();
            $item->product_id = $product_id;
            $item->sku = $sku;
            $item->name_en = $name_en;
            $item->name_ar = $name_ar;
            $item->description_en = $description_en;
            $item->description_ar = $description_ar;
            $item->url_en = $url_en;
            $item->url_ar = $url_ar;
            $item->price = $price;
            $item->price_after_discount = $price_after_discount;
            $item->discount_ratio = $ratio;
            $item->currency = $currency;
            $item->brand_value = $brand_value;
            $item->category = $category;
            $item->status = $status;
            $item->stock_quantity = $stock_quantity;
            $item->tags = $tags;
            $item->texture = $texture;
            $item->skin_type = $skin_type;
            $item->setUpdatedAt(null);
            $item->save();

            if (!empty($all_options)) {
                $options = $all_options;


                foreach ($options as $option) {
                    $option_type = $option['name'];
                    $option_id = $option['option_id'];
                    $product_option_id = $option['product_option_id'];

                    if (!empty($option['option_value'])) {
                        foreach ($option['option_value'] as $opt_val){
                            if (!empty($opt_val['specials'])) {
                                foreach ($opt_val['specials'] as $details) {
                                    if(isset($details['price_formated'])){
                                        $option_discount_price = explode(" ريال ", $details['price_formated'])[0];
                                    }else{
                                        $option_discount_price = $price_after_discount;
                                    }

                                    if(isset($details['discount_ratio'])){
                                        $option_ratio = $details['discount_ratio'];
                                    }else{
                                        $option_ratio = $ratio;
                                    }
                                }
                            } else {
                                $option_discount_price = $price_after_discount;
                                $option_ratio = $ratio;
                            }

                            $option_sku = $opt_val['sku'];
                            $option_name = $opt_val['name'];
                            $option_price = explode(" ريال ", $opt_val['price_formated'])[0];
                            $option_currency = explode(" ", $opt_val['price_formated'])[1];
                            $option_color = $opt_val['hex_color'];
                            $option_active = $opt_val['active'];
                            $option_stock = $opt_val['stock'];
                            $option_stock_quantity = $opt_val['quantity'];


                            $opt_product = NiceoneProducts::Where('sku', '=', $sku.'_OP_'.$option_sku.'_'.$option_name)->get();
                            if(count($opt_product) == 0){
                                $optionsitem = new NiceoneProducts();
                                $optionsitem->product_id = $product_id;
                                $optionsitem->sku = $sku.'_OP_'.$option_sku.'_'.$option_name;
                                $optionsitem->name_en = $name_en;
                                $optionsitem->name_ar = $name_ar;
                                $optionsitem->description_en = $description_en;
                                $optionsitem->description_ar = $description_ar;
                                $optionsitem->url_en = $url_en;
                                $optionsitem->url_ar = $url_ar;
                                $optionsitem->price = $option_price;
                                $optionsitem->price_after_discount = $option_discount_price;
                                $optionsitem->discount_ratio = $option_ratio;
                                $optionsitem->currency = $option_currency;
                                $optionsitem->brand_value = $brand_value;
                                $optionsitem->category = $category;
                                $optionsitem->status = $status;
                                $optionsitem->stock_quantity = $option_stock_quantity;
                                $optionsitem->tags = $tags;
                                $optionsitem->texture = $texture;
                                $optionsitem->skin_type = $skin_type;
                                $optionsitem->setUpdatedAt(null);
                                $optionsitem->save();

                                $niceone_pro_id = $optionsitem->id;

                                NiceoneOptions::SaveData($niceone_pro_id, $option_type, $option_sku, $option_name, $option_price, $option_discount_price, $option_ratio,
                                    $option_currency, $option_color, $option_active, $option_stock, $option_stock_quantity,  $option_id,  $product_option_id);
                            }
                        }
                    }
                }
            }
            return response()->json('added product in db', 200);
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveData Niceone Error', var_export($e->getMessage(), true), 'Error');
        }
    }
    public static function SaveOptionAsProduct($all_options, $sku, $id){
        $options = $all_options;
        foreach ($options as $option) {
            $option_type = $option['name'];
            $option_id = $option['option_id'];
            $product_option_id = $option['product_option_id'];

            if (!empty($option['option_value'])) {
                foreach ($option['option_value'] as $opt_val){
                    if (!empty($opt_val['specials'])) {
                        foreach ($opt_val['specials'] as $details2) {
                            $option_discount_price = explode(" ريال ", $details2['price_formated'])[0];
                            $option_ratio = $details2['discount_ratio'];
                            break;
                        }
                    } else {
                        $option_discount_price = 0;
                        $option_ratio = 0;
                    }

                    $sku1 = $sku.'_OP_'.$opt_val['sku'];

                    $niceone_pro_id = $id;
                    $option_sku = $opt_val['sku'];
                    $option_name = $opt_val['name'];
                    $option_price = explode(" ريال ", $opt_val['price_formated'])[0];
                    $option_currency = explode(" ", $opt_val['price_formated'])[1];
                    $option_color = $opt_val['hex_color'];
                    $option_active = $opt_val['active'];
                    $option_stock = $opt_val['stock'];
                    $option_stock_quantity = $opt_val['quantity'];

                    NiceoneOptions::SaveData($niceone_pro_id, $option_type, $option_sku, $option_name, $option_price, $option_discount_price, $option_ratio,
                        $option_currency, $option_color, $option_active, $option_stock, $option_stock_quantity,  $option_id,  $product_option_id);
                }
            }
        }
    }


    public static function UpdateData($product_id, $sku, $name_ar, $name_en, $description_en, $description_ar, $url_en,
                                      $url_ar, $price, $price_after_discount, $ratio, $currency, $brand_value, $category,
                                      $status, $tags, $stock_quantity, $texture, $skin_type, $all_options)
    {
        try {
            echo 'update';
            $data = [
                "product_id" => $product_id,
                "sku" => $sku,
                "name_en" => $name_en,
                "name_ar" => $name_ar,
                "description_en" => $description_en,
                "description_ar" => $description_ar,
                "url_en" => $url_en,
                "url_ar" => $url_ar,
                "price" => $price,
                "price_after_discount" => $price_after_discount,
                "discount_ratio" => $ratio,
                "currency" => $currency,
                "brand_value" => $brand_value,
                "category" => $category,
                "status" => $status,
                "stock_quantity" => $stock_quantity,
                "tags" => $tags,
                "texture" => $texture,
                "skin_type" => $skin_type,
                "updated_at" => date("Y-m-d H:i:s"),
            ];

            $update_pro = DB::table('niceone_products')
                ->where('sku', '=', $sku)
                ->update($data);

            //$product = NiceoneProducts::Where('sku', '=', $sku)->select('id')->first();
            if (!empty($all_options)) {
                $options = $all_options;
                foreach ($options as $option) {
                    $option_type = $option['name'];
                    $option_id = $option['option_id'];
                    $product_option_id = $option['product_option_id'];

                    if (!empty($option['option_value'])) {
                        foreach ($option['option_value'] as $opt_val){
                            if (!empty($opt_val['specials'])) {
                                foreach ($opt_val['specials'] as $details) {
                                    if(isset($details['price_formated'])){
                                        $option_discount_price = explode(" ريال ", $details['price_formated'])[0];
                                    }else{
                                        $option_discount_price = $price_after_discount;
                                    }

                                    if(isset($details['discount_ratio'])){
                                        $option_ratio = $details['discount_ratio'];
                                    }else{
                                        $option_ratio = $ratio;
                                    }
                                }
                            } else {
                                $option_discount_price = $price_after_discount;
                                $option_ratio = $ratio;
                            }

                            $option_sku = $opt_val['sku'];
                            $option_name = $opt_val['name'];
                            $option_price = explode(" ريال ", $opt_val['price_formated'])[0];
                            $option_currency = explode(" ", $opt_val['price_formated'])[1];
                            $option_color = $opt_val['hex_color'];
                            $option_active = $opt_val['active'];
                            $option_stock = $opt_val['stock'];
                            $option_stock_quantity = $opt_val['quantity'];

                            $data2 = [
                                "product_id" => $product_id,
                                "sku" => $sku.'_OP_'.$option_sku.'_'.$option_name,
                                "name_en" => $name_en,
                                "name_ar" => $name_ar,
                                "description_en" => $description_en,
                                "description_ar" => $description_ar,
                                "url_en" => $url_en,
                                "url_ar" => $url_ar,
                                "price" => $option_price,
                                "price_after_discount" => $option_discount_price,
                                "discount_ratio" => $option_ratio,
                                "currency" => $option_currency,
                                "brand_value" => $brand_value,
                                "category" => $category,
                                "status" => $status,
                                "stock_quantity" => $option_stock_quantity,
                                "tags" => $tags,
                                "texture" => $texture,
                                "skin_type" => $skin_type,
                                "updated_at" => date("Y-m-d H:i:s"),
                            ];

                            $update_pro = DB::table('niceone_products')
                                ->where('sku', '=', $sku.'_OP_'.$option_sku.'_'.$option_name)
                                ->update($data2);
                            $opt_product = NiceoneProducts::Where('sku', '=', $sku.'_OP_'.$option_sku.'_'.$option_name)->select('id')->first();

                            $niceone_pro_id = $opt_product->id;

                            NiceoneOptions::UpdateData($niceone_pro_id, $option_type, $option_sku, $option_name, $option_price, $option_discount_price, $option_ratio,
                                $option_currency, $option_color, $option_active, $option_stock, $option_stock_quantity,  $option_id,  $product_option_id);

                        }
                    }
                }
               }

            return response()->json('updated product in db', 200);
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'update data Niceone Error', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function GetAllProductsForNiceone()
    {
        try {

        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Scrapping products from Niceone Error', var_export($e->getMessage(), true), 'Error');
        }
    }
}
