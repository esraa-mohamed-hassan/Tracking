<?php

namespace App;

use App\MaestaProducts;
use Illuminate\Database\Eloquent\Model;

class UpdateMaestaPricesForProducts extends Model
{
    protected $table = 'update_maesta_price';
    protected $fillable = [
        'sku', 'price', 'price_after_discount', 'last_update',
    ];

    public static function SavePricesAllProducts($sku, $price, $price_after_discount)
    {
        try {
            $item = new UpdateMaestaPricesForProducts();
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
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SavePricesAllProducts Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function UpdatePricesForMaesta()
    {
        try {
            $count_pages = MaestaProducts::CountAllProductsMaesta();
            if (env('APP_ENV') == 'local') {
                $env_url =  env("Magento_URL_LIVE");
            } else {
                $env_url =  env("Magento_URL");
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
                            $sku = $res['sku'];
                            $price = $res['price'];
                            $custom_attributes = $res['custom_attributes'];
                            foreach ($custom_attributes as $attr) {
                                if ($attr['attribute_code'] == 'special_price') {
                                    $price_after_discount = $attr['value'];
                                }
                            }
                            UpdateMaestaPricesForProducts::SavePricesAllProducts($sku, $price, $price_after_discount);
                        }
                    }
                }
            }
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForMaesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}