<?php

namespace App;

use App\MaestaProducts;
use Illuminate\Database\Eloquent\Model;

class MaestaCategory extends Model
{
    protected $table = 'maesta_categories';
    protected $fillable = [
        'category_id', 'name', 'is_active', 'level', 'product_count',
    ];

    public static function GetCategories()
    {
        try {
            if (env('APP_ENV') == 'local') {
                $env_url = env("Magento_URL_LIVE");
            } else {
                $env_url = env("Magento_URL");
            }
            $url = $env_url . '/rest/V1/categories';
            $params = null;
            $response = MaestaProducts::RequestMaesta($url, $params);
            foreach ($response['children_data'] as $data) {
                $active = $data['is_active'];
                // if ($active == true) {
                $category_id = $data['id'];
                $name = $data['name'];
                $level = $data['level'];
                $product_count = $data['product_count'];
                MaestaCategory::SaveCategories($category_id, $name, $active, $level, $product_count);
                // }
            }
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetCategories Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }

    public static function SaveCategories($category_id, $name, $active, $level, $product_count)
    {
        try {
            echo 'add category';
            $item = new MaestaCategory();
            $item->category_id = $category_id;
            $item->name = $name;
            $item->is_active = $active;
            $item->level = $level;
            $item->product_count = $product_count;
            $item->setUpdatedAt(null);
            $item->save();
        } catch (\Exception $e) {
            $log = new ScrappingLogs();
            $log->ScrappingLogData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'SaveCategories Maesta Error ', var_export($e->getMessage(), true), 'Error');
        }
    }
}
