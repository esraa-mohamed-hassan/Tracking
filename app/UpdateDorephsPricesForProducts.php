<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpdateDorephsPricesForProducts extends Model
{
    protected $table = 'update_dorepha_price';
    protected $fillable = [
        'sku', 'price', 'price_after_discount', 'last_update'
    ];
    static public function SavePricesAllProducts($sku, $price, $price_after_discount)
    {
        try {
            $item = new UpdateDorephsPricesForProducts();
            $item->sku = $sku;
            $item->price = $price;
            $item->price_after_discount = $price_after_discount;
            $item->last_update = date("Y-m-d H:i:s");
            $item->setUpdatedAt(null);
            $item->save();
            var_dump('saved');
            return $item;
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SavePricesAllProducts Dorepha Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    static public function UpdatePricesForDorepha()
    {
        try {
            $count_pages = DorephaProducts::CountAllProductsDorepha();
            for ($i = 1; $i <= $count_pages; $i++) {
                $end_point = 'products';
                $params = [
                    'page' => $i,
                    'per_page' => 100,
                ];
                $result = DorephaProducts::RequestDorepha($end_point, $params);
                var_dump($i);
                if (!empty($result)) {
                    foreach ($result as $res) {
                        $sku = $res->sku;
                        $price = $res->regular_price;
                        $price_after_discount = $res->sale_price;
                        UpdateDorephsPricesForProducts::SavePricesAllProducts($sku, $price, $price_after_discount);
                    }
                }
            }
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForDorepha Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}