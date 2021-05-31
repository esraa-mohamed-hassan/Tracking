<?php

namespace App;

use App\ScrappingLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NiceoneOptions extends Model
{
    protected $table = 'niceone_options';
    protected $fillable = [
        'niceone_pro_id', 'type', 'sku', 'name', 'price', 'price_after_discount', 'discount_ratio', 'currency', 'hex_color', 'active', 'stock', 'stock_quantity', 'option_id', 'product_option_id',

    ];


    public static function SaveData( $niceone_pro_id,  $type,  $sku,  $name,  $price,  $price_after_discount,  $discount_ratio,  $currency,  $hex_color,  $active,  $stock,
                                     $stock_quantity,  $option_id,  $product_option_id) {
        try {
            echo 'add';
            $item = new NiceoneOptions();
            $item->niceone_pro_id = $niceone_pro_id;
            $item->type = $type;
            $item->sku = $sku;
            $item->name = $name;
            $item->price = $price;
            $item->price_after_discount = $price_after_discount;
            $item->discount_ratio = $discount_ratio;
            $item->currency = $currency;
            $item->hex_color = $hex_color;
            $item->active = $active;
            $item->stock = $stock;
            $item->stock_quantity = $stock_quantity;
            $item->option_id = $option_id;
            $item->product_option_id = $product_option_id;
            $item->setUpdatedAt(null);
            $item->save();
            return response()->json('added product in db', 200);
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveData Niceone Options Error', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function UpdateData($niceone_pro_id,  $type,  $sku,  $name,  $price,  $price_after_discount,  $discount_ratio,  $currency,  $hex_color,  $active,  $stock,
                                      $stock_quantity,  $option_id,  $product_option_id) {
        try {
            echo 'update';
            $data = [
                "niceone_pro_id" => $niceone_pro_id,
                "type" => $type,
                "sku" => $sku,
                "name" => $name,
                "price" => $price,
                "price_after_discount" => $price_after_discount,
                "discount_ratio" => $discount_ratio,
                "currency" => $currency,
                "hex_color" => $hex_color,
                "active" => $active,
                "stock" => $stock,
                "stock_quantity" => $stock_quantity,
                "option_id" => $option_id,
                "product_option_id" => $product_option_id,
                "updated_at" => date("Y-m-d H:i:s"),
            ];

            $update_pro = DB::table('niceone_options')
                ->where('sku', '=', $sku)
                ->update($data);

            return response()->json('updated product in db', 200);
        } catch (\Exception $e) {

            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'updatedata Niceone options Error', var_export($e->getMessage(), true), 'Error');
        }
    }

}
