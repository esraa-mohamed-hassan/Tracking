<?php

namespace App;

use App\NiceoneProducts;
use App\ScrappingLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateNiceonePricesForProducts extends Model
{
    protected $table = 'update_niceone_price';
    protected $fillable = [
        'sku', 'price', 'price_after_discount', 'last_update',
    ];

    static public function GetProductsFromTable()
    {
        while (true) {
            try {
                $products = NiceoneProducts::where('pro_status', 'pending')->where('last_update', 'like', date("Y-m-d") . '%')->orderBy('id')->get();
                if (count($products) == 0) {
                    $i = 0;
                    if ($i == 0) {
                        echo 'count zero';
                        NiceoneProducts::query()->update([
                            'pro_status' => 'pending',
                            'last_update' => date("Y-m-d H:i:s"),
                        ]);
                        $all_data[] = UpdateNiceonePricesForProducts::UpdatePricesForNiceone();
                        $i++;
                        echo '$i is :' . $i;
                    } else {
                        echo '$i is greater than zero';
                        break;
                    }
                } else {
                    var_dump('count:' . count($products));
                    $all_data[] = UpdateNiceonePricesForProducts::UpdatePricesForNiceone();
                }
            } catch (\Exception $e) {
                echo 'error';
                $log = new ScrappingLogs();
                $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetProductsFromTable Niceone Error ', var_export($e->getMessage(), true), 'Error');
            }
            break;
        }

    }

    public static function SavePricesAllProducts($sku, $price, $price_after_discount)
    {
        try {
            $item = new UpdateNiceonePricesForProducts();
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
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SavePricesAllProducts Niceone Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function UpdatePricesForNiceone()
    {
        try {
            $products = NiceoneProducts::Where('pro_status', 'pending')->select('product_id')->groupBy('product_id')->orderBy('id')->get();
            var_dump('update_price: '.count($products));
            if (!$products->isEmpty()) {


                foreach ($products as $pro){
                    $price_after_discount = 0.00;
                    $id = $pro->product_id;
                    $url = 'https://niceonesa.com/?route=rest/product_admin/products&id=' . $id;
                    $response = NiceoneProducts::RequestNiceone($url);

                    $sku = $response['data']['isbn'];
                    $price = explode(" ريال ", $response['data']['price_formated'])[0];

                    if (!empty($response['data']['special'])) {
                        foreach ($response['data']['special'] as $details) {
                            if (isset($details['price_formated'])){
                                $price_after_discount = explode(" ريال ", $details['price_formated'])[0];
                            }
                            break;
                        }
                    }

                    if (!empty($sku)) {
                        $products = UpdateNiceonePricesForProducts::Where('sku', $sku)->where('last_update', 'like', date("Y-m-d") . '%')->get();
                        if (count($products) == 0) {
                            UpdateNiceonePricesForProducts::SavePricesAllProducts($sku, $price, $price_after_discount);

                        } else {
                            NiceoneProducts::where('sku', $sku)->update([
                                'pro_status' => 'done',
                                'last_update' => date("Y-m-d H:i:s"),
                            ]);
                        }

                        $options = $response['data']['options'];
                        if(!empty($options)){
                            foreach ($options as $option) {
                                if (!empty($option['option_value'])) {
                                    foreach ($option['option_value'] as $opt_val){
                                        if (!empty($opt_val['specials'])) {
                                            foreach ($opt_val['specials'] as $details) {
                                                if(isset($details['price_formated'])){
                                                    $option_discount_price = explode(" ريال ", $details['price_formated'])[0];
                                                }else{
                                                    $option_discount_price = $price_after_discount;
                                                }
                                            }
                                        } else {
                                            $option_discount_price = $price_after_discount;
                                        }

                                        $option_sku = $sku.'_OP_'.$opt_val['sku'].'_'.$opt_val['name'];
                                        $option_price = explode(" ريال ", $opt_val['price_formated'])[0];
                                        $opt_products = UpdateNiceonePricesForProducts::Where('sku', $option_sku)->where('last_update', 'like', date("Y-m-d") . '%')->get();

                                        if(count($opt_products) == 0){
                                            UpdateNiceonePricesForProducts::SavePricesAllProducts($option_sku, $option_price, $option_discount_price);
                                        }else{
                                            NiceoneProducts::where('sku', $option_sku)->update([
                                                'pro_status' => 'done',
                                                'last_update' => date("Y-m-d H:i:s"),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        NiceoneProducts::where('product_id', $id)->update([
                            'pro_status' => 'done',
                            'last_update' => date("Y-m-d H:i:s"),
                        ]);
                    }
                    else {
                        $new_sku = 'SCR_' . $response['data']['sku'];
                        $products = UpdateNiceonePricesForProducts::Where('sku', $new_sku)->where('last_update', 'like', date("Y-m-d") . '%')->get();
                        if (count($products) == 0) {
                            UpdateNiceonePricesForProducts::SavePricesAllProducts($new_sku, $price, $price_after_discount);

                        } else {
                            NiceoneProducts::where('sku', $new_sku)->update([
                                'pro_status' => 'done',
                                'last_update' => date("Y-m-d H:i:s"),
                            ]);
                        }

                        $options = $response['data']['options'];
                        if(!empty($options)){
                            foreach ($options as $option) {
                                if (!empty($option['option_value'])) {
                                    foreach ($option['option_value'] as $opt_val){
                                        if (!empty($opt_val['specials'])) {
                                            foreach ($opt_val['specials'] as $details) {
                                                if(isset($details['price_formated'])){
                                                    $option_discount_price = explode(" ريال ", $details['price_formated'])[0];
                                                }else{
                                                    $option_discount_price = $price_after_discount;
                                                }
                                            }
                                        } else {
                                            $option_discount_price = $price_after_discount;
                                        }

                                        $option_sku = $new_sku.'_OP_'.$opt_val['sku'].'_'.$opt_val['name'];
                                        $option_price = explode(" ريال ", $opt_val['price_formated'])[0];
                                        $opt_products = UpdateNiceonePricesForProducts::Where('sku', $option_sku)->where('last_update', 'like', date("Y-m-d") . '%')->get();
                                        if(count($opt_products) == 0){
                                           UpdateNiceonePricesForProducts::SavePricesAllProducts($option_sku, $option_price, $option_discount_price);
                                        }else{
                                            NiceoneProducts::where('sku', $option_sku)->update([
                                                'pro_status' => 'done',
                                                'last_update' => date("Y-m-d H:i:s"),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }

                        NiceoneProducts::where('product_id', $id)->update([
                            'pro_status' => 'done',
                            'last_update' => date("Y-m-d H:i:s"),
                        ]);
                    }
                }
                return 'Saved all update prices';
            }
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdatePricesForNiceone Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
