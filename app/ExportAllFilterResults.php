<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExportAllFilterResults extends Model
{
    public static function GetItems($data)
    {
        $items = [];
        foreach ($data as $k => $item) {
            $sku = $k;
            $items[$sku]['sku'] = $sku;
            $items[$sku]['name'] = $item['name'];
            $items[$sku]['description'] = strip_tags($item['description']);
            $items[$sku]['category'] = $item['category'];
            if (isset($item['golden'])) {
                if($item['golden']['golden_discount_price'] == null && $item['golden']['golden_price'] == null){
                    $items[$sku]['golden_price'] = 'Not Found';
                    $items[$sku]['golden_discount_price'] = 'Not Found';
                }else {
                    $items[$sku]['golden_price'] = $item['golden']['golden_price'];

                    if ($item['golden']['golden_discount_price'] == '0.00' || $item['golden']['golden_discount_price'] == '0') {
                        $items[$sku]['golden_discount_price'] = $item['golden']['golden_price'];
                    } else {
                        $items[$sku]['golden_discount_price'] = $item['golden']['golden_discount_price'];
                    }
                }
            }

            if (isset($item['dorepha'])) {
                if($item['dorepha']['dorepha_discount_price'] == null && $item['dorepha']['dorepha_price'] == null){
                    $items[$sku]['dorepha_price'] = 'Not Found';
                    $items[$sku]['dorepha_discount_price'] = 'Not Found';
                }else{
                    $items[$sku]['dorepha_price'] = $item['dorepha']['dorepha_price'];

                    if ($item['dorepha']['dorepha_discount_price'] == '0.00' || $item['dorepha']['dorepha_discount_price'] == '0') {
                        $items[$sku]['dorepha_discount_price'] = $item['dorepha']['dorepha_price'];
                    } else {
                        $items[$sku]['dorepha_discount_price'] = $item['dorepha']['dorepha_discount_price'];
                    }
                }
            }

            if (isset($item['maesta'])) {
                if($item['maesta']['maesta_discount_price'] == null && $item['maesta']['maesta_price'] == null){
                    $items[$sku]['maesta_price'] = 'Not Found';
                    $items[$sku]['maesta_discount_price'] = 'Not Found';
                }else {
                    $items[$sku]['maesta_price'] = $item['maesta']['maesta_price'];

                    if ($item['maesta']['maesta_discount_price'] == '0.00' || $item['maesta']['maesta_discount_price'] == '0') {
                        $items[$sku]['maesta_discount_price'] = $item['maesta']['maesta_price'];
                    } else {
                        $items[$sku]['maesta_discount_price'] = $item['maesta']['maesta_discount_price'];
                    }
                }
            }

            if (isset($item['niceone'])) {
                if($item['niceone']['niceone_discount_price'] == null && $item['niceone']['niceone_price'] == null) {
                    $items[$sku]['niceone_price'] = 'Not Found';
                    $items[$sku]['niceone_discount_price'] = 'Not Found';
                }else{
                    $items[$sku]['niceone_price'] = $item['niceone']['niceone_price'];

                    if ($item['niceone']['niceone_discount_price'] == '0.00' || $item['niceone']['niceone_discount_price'] == '0') {
                        $items[$sku]['niceone_discount_price'] = $item['niceone']['niceone_price'];
                    } else {
                        $items[$sku]['niceone_discount_price'] = $item['niceone']['niceone_discount_price'];
                    }
                }
            }
        }
        return $items;
    }
}
