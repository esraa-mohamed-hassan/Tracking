<?php

namespace App;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DorephaProducts extends Model
{
    protected $table = 'dorepha_products';
    protected $fillable = [
        'product_id', 'name', 'slug', 'sku', 'price', 'price_after_discount', 'category', 'description', 'pro_type', 'pro_status', 'stock_quantity', 'stock_status','url',
        'brand_value','size', 'color', 'concentration', 'texture', 'skin_type', 'area_of_apply',
    ];


    static public function RequestDorepha($end_point, $params)
    {
        try {
            $woocommerce = new Client(
                env("WooCommerce_URL"),
                env("WooCommerce_CONSUMER_KEY"),
                env("WooCommerce_CONSUMER_SECRET"),
                ['version' => 'wc/v3']
            );
            $data = $woocommerce->get($end_point,$params);
            return $data;
        } catch (HttpClientException $e) {
            $data = "\n Exception Caught" . $e->getMessage();
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'CountAllProductsDorepha Error ', var_export($data, true), 'Error');
        };
    }

    static public function CountAllProductsDorepha()
    {
        $end_point = 'reports/products/totals';
        $params = [];
        $response = DorephaProducts::RequestDorepha($end_point, $params);
        $key = 'total';
        $products_count = array_sum(array_column($response, $key));
        $count_pages = round($products_count / 100);
        $check_count = $count_pages * 100;
        if ($products_count > $check_count) {
            $count_pages += 5;
        }
        return $count_pages;
    }

    static public function SaveDataPro($product_id, $name, $slug, $sku, $price, $price_after_discount, $categorie, $description, $pro_type, $pro_status, $stock_quantity, $stock_status, $url){
        try {
            $item = new DorephaProducts();
            $item->product_id = $product_id;
            $item->name = $name;
            $item->slug = $slug;
            $item->sku = $sku;
            $item->price = $price;
            $item->price_after_discount = $price_after_discount;
            $item->category = $categorie;
            $item->description = $description;
            $item->pro_type = $pro_type;
            $item->pro_status = $pro_status;
            $item->stock_quantity = $stock_quantity;
            $item->stock_status = $stock_status;
            $item->url = $url;
            $item->setUpdatedAt(null);
            $item->save();
//            $log = new ScrappingLogs();
//            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'saved results from Dorepha in dorepha_products table', var_export($item, true), 'Error');
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveDataPro Dorepha Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    static public function UpdateDataPro($product_id, $name, $slug, $sku, $price, $price_after_discount, $categorie, $description, $pro_type, $pro_status, $stock_quantity, $stock_status, $url){
        try {
            $data = [
                'product_id' => $product_id,
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'price' => $price,
                'price_after_discount' => $price_after_discount,
                'category' => $categorie,
                'description' => $description,
                'pro_type' => $pro_type,
                'pro_status' => $pro_status,
                'stock_quantity' => $stock_quantity,
                'stock_status' => $stock_status,
                'url' => $url,
                'updated_at' => date("Y-m-d H:i:s"),

            ];
            $update_pro = DB::table('dorepha_products')
                ->where('sku', '=', $sku)
                ->update($data);

//            $log = new ScrappingLogs();
//            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdateDataPro results from Dorepha', var_export($data, true), 'Error');
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdateDataPro Dorepha Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
