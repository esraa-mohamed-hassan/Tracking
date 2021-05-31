<?php

namespace App;

use App\ScrappingLogs;
use Illuminate\Database\Eloquent\Model;

class MaestaSize extends Model
{
    protected $table = 'maesta_size';
    protected $fillable = [
        'size_id', 'name',
    ];

    public static function GetSize()
    {
        try {
            if (env('APP_ENV') == 'local') {
                $env_url = env("Magento_URL_LIVE");
            } else {
                $env_url = env("Magento_URL");
            }
            $url = $env_url . '/rest/V1/products/attributes/size/options';
            $params = null;
            $response = MaestaProducts::RequestMaesta($url, $params);
            foreach ($response as $data) {
                if ($data['value'] != '' && $data['label'] != '') {
                    $size_id = $data['value'];
                    $name = $data['label'];
                    MaestaSize::SaveSize($size_id, $name);
                }
            }
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetSize Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function SaveSize($size_id, $name)
    {
        try {
            echo 'add size';
            $item = new MaestaSize();
            $item->size_id = $size_id;
            $item->name = $name;
            $item->setUpdatedAt(null);
            $item->save();
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveSize Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
