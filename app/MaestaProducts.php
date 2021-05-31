<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MaestaProducts extends Model
{
    protected $table = 'maesta_products';
    protected $fillable = [
        'product_id', 'name', 'sku', 'price', 'price_after_discount', 'description', 'pro_type', 'pro_status', 'visibility', 'url', 'category',
        'brand_value', 'size', 'color', 'concentration', 'texture', 'skin_type', 'area_of_apply',
    ];

    public static function RequestMaesta($url, $params)
    {
        ini_set('max_execution_time', 0);

        try {
            if (env('APP_ENV') == 'local') {
                $token = env("Magento_TOKEN_LIVE");
            } else {
                $token = env("Magento_TOKEN");
            }

            $response = Http::withToken($token)->timeout(60)->get($url, $params);
            if ($response->status() == 200) {
                return $response->json();
            } else if ($response->serverError()) {
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'RequestMaesta serverError ', var_export($response->body(), true), 'Error');
                return $response->throw();
            } else {
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'RequestMaesta Error ', var_export($response, true), 'Error');
                return $response->throw();
            }
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'RequestMaesta Error ', var_export($response, true), 'Error');
        }
    }

    public static function CountAllProductsMaesta()
    {
        try {
            if (env('APP_ENV') == 'local') {
                $env_url = env("Magento_URL_LIVE");
            } else {
                $env_url = env("Magento_URL");
            }
            $url = $env_url . '/rest/V1/products';
            $params = ['searchCriteria[pageSize]' => '1'];
            $response = MaestaProducts::RequestMaesta($url, $params);
            $products_count = $response['total_count'];
            $count_pages = round($products_count / 500);
            $check_count = $count_pages * 500;
            if ($products_count > $check_count) {
                $count_pages += 5;
            }
            return $count_pages;

        } catch (\Exception $e) {
            $data = "\n Exception Caught" . $e->getMessage();
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'CountAllProductsMaesta Error ', var_export($data, true), 'Error');
        };
    }

    public static function SaveDataPro($product_id, $name, $sku, $price, $price_after_discount, $description, $pro_type,
        $pro_status, $visibility, $url, $brand_name, $category_name, $size_name, $skin_type_name) {
        try {
            $item = new MaestaProducts();
            $item->product_id = $product_id;
            $item->name = $name;
            $item->sku = $sku;
            $item->price = $price;
            $item->price_after_discount = $price_after_discount;
            $item->description = $description;
            $item->pro_type = $pro_type;
            $item->pro_status = $pro_status;
            $item->visibility = $visibility;
            $item->url = $url;
            $item->brand_value = $brand_name;
            $item->category = $category_name;
            $item->size = $size_name;
            $item->area_of_apply = $skin_type_name;
            $item->setUpdatedAt(null);
            $item->save();
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveDataPro Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function UpdateDataPro($product_id, $name, $sku, $price, $price_after_discount, $description, $pro_type,
        $pro_status, $visibility, $url, $brand_name, $category_name, $size_name, $skin_type_name)
    {
        try {
            $data = [
                'product_id' => $product_id,
                'name' => $name,
                'sku' => $sku,
                'price' => $price,
                'price_after_discount' => $price_after_discount,
                'description' => $description,
                'pro_type' => $pro_type,
                'pro_status' => $pro_status,
                'visibility' => $visibility,
                'url' => $url,
                'brand_value' => $brand_name,
                'category' => $category_name,
                'size' => $size_name,
                'area_of_apply' => $skin_type_name,
                'updated_at' => date("Y-m-d H:i:s"),
            ];
            $update_pro = DB::table('maesta_products')
                ->where('sku', '=', $sku)
                ->update($data);

        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdateDataPro Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
