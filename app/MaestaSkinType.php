<?php

namespace App;

use App\ScrappingLogs;
use Illuminate\Database\Eloquent\Model;

class MaestaSkinType extends Model
{
    protected $table = 'maesta_skin_type';
    protected $fillable = [
        'skin_type_id', 'name',
    ];

    public static function GetSkinType()
    {
        try {
            if (env('APP_ENV') == 'local') {
                $env_url = env("Magento_URL_LIVE");
            } else {
                $env_url = env("Magento_URL");
            }
            $url = $env_url . '/rest/V1/products/attributes/make_up/options';
            $params = null;
            $response = MaestaProducts::RequestMaesta($url, $params);
            foreach ($response as $data) {
                if ($data['value'] != '' && $data['label'] != '') {
                    $skin_type_id = $data['value'];
                    $name = $data['label'];
                    MaestaSkinType::SaveSkinType($skin_type_id, $name);
                }
            }
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetSkinType Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function SaveSkinType($skin_type_id, $name)
    {
        try {
            echo 'add skin_type';
            $item = new MaestaSkinType();
            $item->skin_type_id = $skin_type_id;
            $item->name = $name;
            $item->setUpdatedAt(null);
            $item->save();
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveSkinType Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
