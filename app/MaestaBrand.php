<?php

namespace App;

use App\MaestaProducts;
use Illuminate\Database\Eloquent\Model;

class MaestaBrand extends Model
{
    protected $table = 'maesta_brands';
    protected $fillable = [
        'brand_id', 'name',
    ];

    public static function GetBrands()
    {
        try {
            if (env('APP_ENV') == 'local') {
                $env_url = env("Magento_URL_LIVE");
            } else {
                $env_url = env("Magento_URL");
            }
            $url = $env_url . '/rest/V1/products/attributes/manufacturer/options';
            $params = null;
            $response = MaestaProducts::RequestMaesta($url, $params);
            foreach ($response as $data) {
                if ($data['value'] != '' && $data['label'] != '') {
                    $brand_id = $data['value'];
                    $name = $data['label'];
                    MaestaBrand::SaveBrands($brand_id, $name);
                }
            }
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetBrands Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function SaveBrands($brand_id, $name)
    {
        try {
            echo 'add brand';
            $item = new MaestaBrand();
            $item->brand_id = $brand_id;
            $item->name = $name;
            $item->setUpdatedAt(null);
            $item->save();
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveBrands Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
