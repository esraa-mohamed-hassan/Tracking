<?php

namespace App\Http\Controllers;

use App\Exports\ExportFilterResults;
use App\FiltersLogs;
use App\UpdateDorephsPricesForProducts;
use App\UpdateGoldenScentPricesForProducts;
use App\UpdateMaestaPricesForProducts;
use App\UpdateNiceonePricesForProducts;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF;

class FiltersAndSearchController extends Controller
{
    public function __construct()
    {
        ini_set('max_execution_time', 0);

        $this->middleware('auth');
    }

    public function GetBrandsAndCategories()
    {
        try {
            /* Strat Get all Brands */
            $golden_brands = DB::table('products')->selectRaw('brand_value')
                ->whereNotIn('brand_value', ['', '2899'])
                ->groupBy('brand_value')
                ->get();

            $maesta_brands = DB::table('maesta_products')->selectRaw('brand_value')
                ->whereNotIn('brand_value', [''])
                ->groupBy('brand_value')
                ->get();

            $niceone_brands = DB::table('niceone_products')->selectRaw('brand_value')
                ->groupBy('brand_value')
                ->get();

            $brands = [];
            foreach ($golden_brands as $golden_brand) {
                if (!in_array($golden_brand->brand_value, $brands)) {
                    array_push($brands, $golden_brand->brand_value);
                }
            }
            foreach ($maesta_brands as $maesta_brand) {
                if (!in_array($maesta_brand->brand_value, $brands)) {
                    array_push($brands, $maesta_brand->brand_value);
                }
            }

            foreach ($niceone_brands as $niceone_brand) {
                if (!in_array($niceone_brand->brand_value, $brands)) {
                    array_push($brands, $niceone_brand->brand_value);
                }
            }
            /* End Get all Brands */

            /* Strat Get all Categories */
            $golden_categories = DB::table('products')->selectRaw('category')
                ->whereNotIn('category', ['', '11205', '12005', '12867', '12882', '12917', '14161', '1941', '2170', '2174', '4814', '7803', '8996'])
                ->groupBy('category')
                ->get();

            $dorepha_categories = DB::table('dorepha_products')->selectRaw('category')
                ->whereNotIn('category', [''])
                ->groupBy('category')
                ->get();

            $maesta_categories = DB::table('maesta_products')->selectRaw('category')
                ->whereNotIn('category', ['', 'يوم الأم', 'الأفضل مبيعاً', 'جديد', 'عروض العيد', 'عروض اليوم الوطني', 'عروض اليوم الوطني', 'عطور بـ 99 ريال', 'عروض ميستا', 'Maesta Boxes', 'المنتجات الأفضل مبيعا'])
                ->groupBy('category')
                ->get();

            $niceone_categories = DB::table('niceone_products')->selectRaw('category')
                ->groupBy('category')
                ->get();

            $categories = [];
            foreach ($golden_categories as $golden_category) {
                if (!in_array($golden_category->category, $categories)) {
                    array_push($categories, $golden_category->category);
                }
            }

            foreach ($dorepha_categories as $dorepha_category) {
                if (!in_array($dorepha_category->category, $categories)) {
                    array_push($categories, $dorepha_category->category);
                }
            }

            foreach ($maesta_categories as $maesta_category) {
                if (!in_array($maesta_category->category, $categories)) {
                    array_push($categories, $maesta_category->category);
                }
            }

            foreach ($niceone_categories as $niceone_category) {
                if (!in_array($niceone_category->category, $categories)) {
                    array_push($categories, $niceone_category->category);
                }
            }
            /* End Get all Categories */

            return (['categories' => $categories, 'brands' => $brands]);

        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetBrandsAndCategories Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function index()
    {
        $data = $this->GetBrandsAndCategories();
        return view('search')
            ->with('categories', $data['categories'])
            ->with('brands', $data['brands']);
    }

    public function paginate($items, $perPage = 10, $page, $baseUrl = '/searching#', $options = [])
    {
        try {
            $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

            $items = $items instanceof Collection ?
                $items : Collection::make($items);

            $lap = new LengthAwarePaginator($items->forPage($page, $perPage),
                $items->count(),
                $perPage, $page, $options);

            if ($baseUrl) {
                $lap->setPath($baseUrl);
            }

            return $lap;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'paginate Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function getGoldenDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr)
    {

        $golden_data = DB::table('products as golden')
            ->select('golden.sku as sku', 'golden.name as name', 'golden.description as description',
                DB::raw("golden.sku as 'golden_sku'"),
                DB::raw("golden.price as 'golden_price'"),
                DB::raw("golden.price_after_discount as 'golden_discount_price'"),
                DB::raw("dorepha.price as 'dorepha_price'"),
                DB::raw("dorepha.price_after_discount as 'dorepha_discount_price'"),
                DB::raw("dorepha.stock_status as 'dorepha_stock'"),
                DB::raw("dorepha.sku as 'dorepha_sku'"),
                DB::raw("maesta.sku as 'maesta_sku'"),
                DB::raw("maesta.price as 'maesta_price'"),
                DB::raw("maesta.price_after_discount as 'maesta_discount_price'"),
                DB::raw("niceone.sku as 'niceone_sku'"),
                DB::raw("niceone.price as 'niceone_price'"),
                DB::raw("niceone.price_after_discount as 'niceone_discount_price'"),
                DB::raw("golden.category as 'category'"),
                DB::raw("golden.concentration as 'concentration'"),
                DB::raw("golden.size as 'size'"),
                DB::raw("golden.color as 'color'"),
                DB::raw("golden.texture as 'texture'"),
                DB::raw("golden.skin_type as 'skin_type'"),
                DB::raw("golden.area_of_apply as 'area_of_apply'"),
                DB::raw("golden.brand_value as 'brand_value'")
            )
            ->leftJoin('dorepha_products as dorepha', 'dorepha.sku', 'LIKE', DB::raw("CONCAT('%', golden.sku, '%')"))
            ->leftJoin('maesta_products as maesta', 'maesta.sku', 'LIKE', DB::raw("CONCAT('%', golden.sku, '%')"))
            ->leftJoin('niceone_products as niceone', 'niceone.sku', 'LIKE', DB::raw("CONCAT('%', golden.sku, '%')"))
            ->Where(function ($golden_data) use ($searching) {
                $golden_data->where('golden.name', 'like', '%' . $searching . '%');
                $golden_data->orWhere('golden.sku', 'like', '%' . $searching . '%');
            });

        if (!empty($search_categroy)) {
            $golden_data->Where(function ($golden_data) use ($search_categroy) {
                for ($i = 0; $i < count($search_categroy); $i++) {
                    $golden_data->orwhere('golden.category', 'like', '%' . $search_categroy[$i] . '%');
                }
            });
        }

        if (!empty($search_brand)) {
            $golden_data->Where(function ($golden_data) use ($search_brand) {
                for ($i = 0; $i < count($search_brand); $i++) {
                    $golden_data->orwhere('golden.brand_value', 'like', '%' . $search_brand[$i] . '%');
                }
            });
        }

        if (!empty($search_attr)) {
            switch ($search_attr) {
                case 'concentration':
                    $type = 'concentration';
                    break;
                case 'size':
                    $type = 'size';
                    break;
                case 'color':
                    $type = 'color';
                    break;
                case 'texture':
                    $type = 'texture';
                    break;
                case 'skin_type':
                    $type = 'skin_type';
                    break;
                case 'area_of_apply':
                    $type = 'area_of_apply';
                    break;
            }
            if (!empty($search_selection_attr)) {
                $golden_data->Where(function ($golden_data) use ($type, $search_selection_attr) {
                    for ($i = 0; $i < count($search_selection_attr); $i++) {
                        $golden_data->orwhere('golden.' . $type, 'like', '%' . $search_selection_attr[$i] . '%');
                    }
                });
            }
        }

        return $golden_data;
    }

    public function getDorephaDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr)
    {

        $dorepha_data = DB::table('dorepha_products as dorepha')
            ->select('dorepha.sku as sku', 'dorepha.name as name', 'dorepha.description as description',
                DB::raw("golden.sku as 'golden_sku'"),
                DB::raw("golden.price as 'golden_price'"),
                DB::raw("golden.price_after_discount as 'golden_discount_price'"),
                DB::raw("dorepha.price as 'dorepha_price'"),
                DB::raw("dorepha.price_after_discount as 'dorepha_discount_price'"),
                DB::raw("dorepha.stock_status as 'dorepha_stock'"),
                DB::raw("dorepha.sku as 'dorepha_sku'"),
                DB::raw("maesta.sku as 'maesta_sku'"),
                DB::raw("maesta.price as 'maesta_price'"),
                DB::raw("maesta.price_after_discount as 'maesta_discount_price'"),
                DB::raw("niceone.sku as 'niceone_sku'"),
                DB::raw("niceone.price as 'niceone_price'"),
                DB::raw("niceone.price_after_discount as 'niceone_discount_price'"),
                DB::raw("dorepha.category as 'category'"),
                DB::raw("dorepha.concentration as 'concentration'"),
                DB::raw("dorepha.size as 'size'"),
                DB::raw("dorepha.color as 'color'"),
                DB::raw("dorepha.texture as 'texture'"),
                DB::raw("dorepha.skin_type as 'skin_type'"),
                DB::raw("dorepha.area_of_apply as 'area_of_apply'"),
                DB::raw("dorepha.brand_value as 'brand_value'")
            )
            ->leftJoin('products as golden', 'golden.sku', 'LIKE', DB::raw("CONCAT('%', dorepha.sku, '%')"))
            ->leftJoin('maesta_products as maesta', 'maesta.sku', 'LIKE', DB::raw("CONCAT('%', dorepha.sku, '%')"))
            ->leftJoin('niceone_products as niceone', 'niceone.sku', 'LIKE', DB::raw("CONCAT('%', dorepha.sku, '%')"))
            ->Where(function ($dorepha_data) use ($searching) {
                $dorepha_data->where('dorepha.name', 'like', '%' . $searching . '%');
                $dorepha_data->orWhere('dorepha.sku', 'like', '%' . $searching . '%');
            });

        if (!empty($search_categroy)) {
            $dorepha_data->Where(function ($dorepha_data) use ($search_categroy) {
                for ($i = 0; $i < count($search_categroy); $i++) {
                    $dorepha_data->orwhere('dorepha.category', 'like', '%' . $search_categroy[$i] . '%');
                }
            });
        }

        if (!empty($search_brand)) {
            $dorepha_data->Where(function ($dorepha_data) use ($search_brand) {
                for ($i = 0; $i < count($search_brand); $i++) {
                    $dorepha_data->orwhere('dorepha.brand_value', 'like', '%' . $search_brand[$i] . '%');
                }
            });
        }

        if (!empty($search_attr)) {
            switch ($search_attr) {
                case 'concentration':
                    $type = 'concentration';
                    break;
                case 'size':
                    $type = 'size';
                    break;
                case 'color':
                    $type = 'color';
                    break;
                case 'texture':
                    $type = 'texture';
                    break;
                case 'skin_type':
                    $type = 'skin_type';
                    break;
                case 'area_of_apply':
                    $type = 'area_of_apply';
                    break;
            }
            if (!empty($search_selection_attr)) {
                $dorepha_data->Where(function ($dorepha_data) use ($type, $search_selection_attr) {
                    for ($i = 0; $i < count($search_selection_attr); $i++) {
                        $dorepha_data->orwhere('dorepha.' . $type, 'like', '%' . $search_selection_attr[$i] . '%');
                    }
                });
            }
        }
        return $dorepha_data;
    }

    public function getMaestaDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr)
    {
        $maesta_data = DB::table('maesta_products as maesta')
            ->select('maesta.sku as sku', 'maesta.name as name', 'maesta.description as description',
                DB::raw("golden.sku as 'golden_sku'"),
                DB::raw("golden.price as 'golden_price'"),
                DB::raw("golden.price_after_discount as 'golden_discount_price'"),
                DB::raw("dorepha.price as 'dorepha_price'"),
                DB::raw("dorepha.price_after_discount as 'dorepha_discount_price'"),
                DB::raw("dorepha.stock_status as 'dorepha_stock'"),
                DB::raw("dorepha.sku as 'dorepha_sku'"),
                DB::raw("maesta.sku as 'maesta_sku'"),
                DB::raw("maesta.price as 'maesta_price'"),
                DB::raw("maesta.price_after_discount as 'maesta_discount_price'"),
                DB::raw("niceone.sku as 'niceone_sku'"),
                DB::raw("niceone.price as 'niceone_price'"),
                DB::raw("niceone.price_after_discount as 'niceone_discount_price'"),
                DB::raw("maesta.category as 'category'"),
                DB::raw("maesta.concentration as 'concentration'"),
                DB::raw("maesta.size as 'size'"),
                DB::raw("maesta.color as 'color'"),
                DB::raw("maesta.texture as 'texture'"),
                DB::raw("maesta.skin_type as 'skin_type'"),
                DB::raw("maesta.area_of_apply as 'area_of_apply'"),
                DB::raw("maesta.brand_value as 'brand_value'")
            )
            ->leftJoin('products as golden', 'golden.sku', 'LIKE', DB::raw("CONCAT('%', maesta.sku, '%')"))
            ->leftJoin('dorepha_products as dorepha', 'dorepha.sku', 'LIKE', DB::raw("CONCAT('%', maesta.sku, '%')"))
            ->leftJoin('niceone_products as niceone', 'niceone.sku', 'LIKE', DB::raw("CONCAT('%', maesta.sku, '%')"))
            ->Where(function ($maesta_data) use ($searching) {
                $maesta_data->where('maesta.name', 'like', '%' . $searching . '%');
                $maesta_data->orWhere('maesta.sku', 'like', '%' . $searching . '%');
            });

        if (!empty($search_categroy)) {
            $maesta_data->Where(function ($maesta_data) use ($search_categroy) {
                for ($i = 0; $i < count($search_categroy); $i++) {
                    $maesta_data->orwhere('maesta.category', 'like', '%' . $search_categroy[$i] . '%');
                }
            });
        }

        if (!empty($search_brand)) {
            $maesta_data->Where(function ($maesta_data) use ($search_brand) {
                for ($i = 0; $i < count($search_brand); $i++) {
                    $maesta_data->orwhere('maesta.brand_value', 'like', '%' . $search_brand[$i] . '%');
                }
            });
        }

        if (!empty($search_attr)) {
            switch ($search_attr) {
                case 'concentration':
                    $type = 'concentration';
                    break;
                case 'size':
                    $type = 'size';
                    break;
                case 'color':
                    $type = 'color';
                    break;
                case 'texture':
                    $type = 'texture';
                    break;
                case 'skin_type':
                    $type = 'skin_type';
                    break;
                case 'area_of_apply':
                    $type = 'area_of_apply';
                    break;
            }
            if (!empty($search_selection_attr)) {
                $maesta_data->Where(function ($maesta_data) use ($type, $search_selection_attr) {
                    for ($i = 0; $i < count($search_selection_attr); $i++) {
                        $maesta_data->orwhere('maesta.' . $type, 'like', '%' . $search_selection_attr[$i] . '%');
                    }
                });
            }
        }

        return $maesta_data;
    }

    public function getNiceoneDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr)
    {
        $niceone_data = DB::table('niceone_products as niceone')
            ->select('niceone.sku as sku', 'niceone.name_ar as name', 'niceone.description_ar as description',
                DB::raw("golden.sku as 'golden_sku'"),
                DB::raw("golden.price as 'golden_price'"),
                DB::raw("golden.price_after_discount as 'golden_discount_price'"),
                DB::raw("dorepha.price as 'dorepha_price'"),
                DB::raw("dorepha.price_after_discount as 'dorepha_discount_price'"),
                DB::raw("dorepha.stock_status as 'dorepha_stock'"),
                DB::raw("dorepha.sku as 'dorepha_sku'"),
                DB::raw("maesta.sku as 'maesta_sku'"),
                DB::raw("maesta.price as 'maesta_price'"),
                DB::raw("maesta.price_after_discount as 'maesta_discount_price'"),
                DB::raw("niceone.sku as 'niceone_sku'"),
                DB::raw("niceone.price as 'niceone_price'"),
                DB::raw("niceone.price_after_discount as 'niceone_discount_price'"),
                DB::raw("niceone.category as 'category'"),
                DB::raw("niceone.concentration as 'concentration'"),
                DB::raw("niceone.size as 'size'"),
                DB::raw("niceone.texture as 'texture'"),
                DB::raw("niceone.skin_type as 'skin_type'"),
                DB::raw("niceone.area_of_apply as 'area_of_apply'"),
                DB::raw("options.hex_color as 'color'"),
                DB::raw("niceone.brand_value as 'brand_value'")
            )
            ->leftJoin('niceone_options as options', 'options.niceone_pro_id', '=', 'niceone.id')
            ->leftJoin('products as golden', 'golden.sku', 'LIKE', DB::raw("CONCAT('%', niceone.sku, '%')"))
            ->leftJoin('dorepha_products as dorepha', 'dorepha.sku', 'LIKE', DB::raw("CONCAT('%', niceone.sku, '%')"))
            ->leftJoin('maesta_products as maesta', 'maesta.sku', 'LIKE', DB::raw("CONCAT('%', niceone.sku, '%')"))
            ->Where(function ($niceone_data) use ($searching) {
                $niceone_data->where('niceone.name_ar', 'like', '%' . $searching . '%');
                $niceone_data->orWhere('niceone.sku', 'like', '%' . $searching . '%');
            });

        if (!empty($search_categroy)) {
            $niceone_data->Where(function ($niceone_data) use ($search_categroy) {
                for ($i = 0; $i < count($search_categroy); $i++) {
                    $niceone_data->orwhere('niceone.category', 'like', '%' . $search_categroy[$i] . '%');
                }
            });
        }

        if (!empty($search_brand)) {
            $niceone_data->Where(function ($niceone_data) use ($search_brand) {
                for ($i = 0; $i < count($search_brand); $i++) {
                    $niceone_data->orwhere('niceone.brand_value', 'like', '%' . $search_brand[$i] . '%');
                }
            });
        }

        if (!empty($search_attr)) {
            switch ($search_attr) {
                case 'concentration':
                    $type = 'concentration';
                    break;
                case 'size':
                    $type = 'size';
                    break;
                case 'color':
                    $type = 'color';
                    break;
                case 'texture':
                    $type = 'texture';
                    break;
                case 'skin_type':
                    $type = 'skin_type';
                    break;
                case 'area_of_apply':
                    $type = 'area_of_apply';
                    break;
            }
            if (!empty($search_selection_attr)) {
                $niceone_data->Where(function ($niceone_data) use ($type, $search_selection_attr) {
                    for ($i = 0; $i < count($search_selection_attr); $i++) {
                        $niceone_data->orwhere('niceone.' . $type, 'like', '%' . $search_selection_attr[$i] . '%');
                    }
                });
            }
        }
        return $niceone_data;
    }

    public function getDatafromDB($search, $search_categroy, $search_brand, $search_attr, $search_selection_attr)
    {
        ini_set('max_execution_time', 0);

        try {
            if (!empty($search)) {
                $searching = (int)$search == 0 ? $search : ltrim($search, '0');
            } else {
                $searching = '';
            }

            $golden_data = $this->getGoldenDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr);
            $dorepha_data = $this->getDorephaDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr);
            $maesta_data = $this->getMaestaDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr);
            $niceone_data = $this->getNiceoneDatafromDB($searching, $search_categroy, $search_brand, $search_attr, $search_selection_attr);

            $data1 = $maesta_data->union($niceone_data)->union($dorepha_data)->union($golden_data);

            $data = $data1->get();
            $all_result = [];
            $all_categories = [];
            $all_brands = [];
            $all_colors = [];
            $all_textures = [];
            $all_skin_types = [];
            $all_area_of_apply = [];
            $all_concentration = [];
            $all_size = [];
            $all_attr = [];

            foreach ($data as $item) {
                if (!empty($item->dorepha_sku)) {
                    if (preg_match("~^0\d+$~", $item->dorepha_sku)) {
                        $sku = $item->dorepha_sku;
                    } else {
                        $sku = $item->sku;
                    }
                } elseif (!empty($item->maesta_sku)) {
                    if (preg_match("~^0\d+$~", $item->maesta_sku)) {
                        $sku = $item->maesta_sku;
                    } else {
                        $sku = $item->sku;
                    }
                } else {
                    $sku = $item->sku;
                }

                if (!empty($item->golden_price)) {
                    $price = $item->golden_price;
                } elseif (!empty($item->dorepha_price)) {
                    $price = $item->dorepha_price;
                } elseif (!empty($item->maesta_price)) {
                    $price = $item->maesta_price;
                } elseif (!empty($item->niceone_price)) {
                    $price = $item->niceone_price;
                }
                $new_sku = (int)$sku == 0 ? $sku : ltrim($sku, '0');
                $all_result[$new_sku]['sku'] = $new_sku;
                $all_result[$new_sku]['name'] = $item->name;
                $all_result[$new_sku]['description'] = $item->description;
                $all_result[$new_sku]['category'] = $item->category;
                $all_result[$new_sku]['price'] = $price;
                $all_result[$new_sku]["golden"]["golden_price"] = $item->golden_price;
                $all_result[$new_sku]["golden"]["golden_discount_price"] = $item->golden_discount_price;
                $all_result[$new_sku]["concentration"] = $item->concentration;
                $all_result[$new_sku]["brand_value"] = $item->brand_value;
                $all_result[$new_sku]["size"] = $item->size;
                $all_result[$new_sku]["color"] = $item->color;
                $all_result[$new_sku]["texture"] = $item->texture;
                $all_result[$new_sku]["skin_type"] = $item->skin_type;
                $all_result[$new_sku]["area_of_apply"] = $item->area_of_apply;
                $all_result[$new_sku]["dorepha"]["dorepha_price"] = $item->dorepha_price;
                $all_result[$new_sku]["dorepha"]["dorepha_discount_price"] = $item->dorepha_discount_price;
                $all_result[$new_sku]["dorepha"]["dorepha_stock"] = $item->dorepha_stock;
                $all_result[$new_sku]["maesta"]["maesta_price"] = $item->maesta_price;
                $all_result[$new_sku]["maesta"]["maesta_discount_price"] = $item->maesta_discount_price;
                $all_result[$new_sku]["niceone"]["niceone_price"] = $item->niceone_price;
                $all_result[$new_sku]["niceone"]["niceone_discount_price"] = $item->niceone_discount_price;

                if (!empty($item->category) && !in_array($item->category, $all_categories)) {
                    array_push($all_categories, $item->category);
                }

                if (!empty($item->size) && !in_array($item->size, $all_size)) {
                    array_push($all_size, $item->size);
                }

                if (!empty($item->brand_value) && !in_array($item->brand_value, $all_brands)) {
                    array_push($all_brands, $item->brand_value);
                }

                if (!empty($item->color) && !in_array($item->color, $all_colors)) {
                    array_push($all_colors, $item->color);
                }

                if (!empty($item->texture) && !in_array($item->texture, $all_textures)) {
                    array_push($all_textures, $item->texture);
                }

                if (!empty($item->skin_type) && !in_array($item->skin_type, $all_skin_types)) {
                    array_push($all_skin_types, $item->skin_type);
                }

                if (!empty($item->area_of_apply) && !in_array($item->area_of_apply, $all_area_of_apply)) {
                    array_push($all_area_of_apply, $item->area_of_apply);
                }

                if (!empty($item->concentration) && !in_array($item->concentration, $all_concentration)) {
                    array_push($all_concentration, $item->concentration);
                }
            }

            $all_res = [
                'categories' => count($all_categories) != 0 ? $all_categories : $search_categroy,
                'size' => $all_size,
                'brands' => $all_brands,
                'colors' => $all_colors,
                'textures' => $all_textures,
                'skin_types' => $all_skin_types,
                'area_of_apply' => $all_area_of_apply,
                'concentration' => $all_concentration,
                'attributes' => $all_attr,
                'results' => $all_result
            ];

            return $all_res;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'getDatafromDB Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function search(Request $request)
    {
        try {

            if ($request->method() == 'POST') {
                $request->session()->forget('results_from_search');
                $search_categroy_link = $request->input('search_categroy_link');

                if (empty($search_categroy_link)) {
                    $search_categroy = $request->input('search_categroy');
                } else {
                    $search_categroy = array($search_categroy_link);
                    $_REQUEST['search_categroy'] = $search_categroy;
                }

                $search_brand = $request->input('search_brand');
                $search_attr = $request->input('search_attr');
                $search_selection_attr = $request->input('search_selection_attr');
                $search = trim($request->input('search'));

                $data = $this->getDatafromDB($search, $search_categroy, $search_brand, $search_attr, $search_selection_attr);

                $all_data = $this->paginate($data['results'], 10, 1, '/searching#', []);

                $request->session()->put('results_from_search', $data['results']);

                return view('result_search')
                    ->with('search', $search)
                    ->with('data', $all_data)
                    ->with('all_result', $data['results'])
                    ->with('sizes', $data['size'])
                    ->with('colors', $data['colors'])
                    ->with('textures', $data['textures'])
                    ->with('skin_types', $data['skin_types'])
                    ->with('area_of_applies', $data['area_of_apply'])
                    ->with('concentrations', $data['concentration'])
                    ->with('categories', $data['categories'])
                    ->with('attributes', $data['attributes'])
                    ->with('brands', $data['brands']);
            } else {
                return redirect('/search');
            }
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'search Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function downloadAsExcel(Request $request)
    {
        try {
            if ($request->session()->has('results_from_search')) {
                $results = $request->session()->get('results_from_search');
            }
            $filename = 'FilterResults' . time() . ".xlsx";
            $file_path = 'public/ExcelFiles/' . $filename;
            Excel::store(new ExportFilterResults($results), $file_path);
            return Excel::download(new ExportFilterResults($results), $filename);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'downloadAsExcel Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function GetConcentration()
    {
        try {
            $concentrations = DB::table('products')->selectRaw('concentration')
                ->whereNotIn('concentration', ['', '13028'])
                ->groupBy('concentration')
                ->get();
            return response(['status' => 'success', 'data' => $concentrations]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetConcentration Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function GetSize()
    {
        try {
            $size = DB::table('products')->selectRaw('size')
                ->whereNotIn('size', [''])
                ->groupBy('size')
                ->get();
            return response(['status' => 'success', 'data' => $size]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetSize Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function GetColor()
    {
        try {
            $color = DB::table('products')->selectRaw('color')
                ->whereNotIn('color', [''])
                ->groupBy('color')
                ->get();
            return response(['status' => 'success', 'data' => $color]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetColor Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function GetTexture()
    {
        try {
            $texture = DB::table('products')->selectRaw('texture')
                ->whereNotIn('texture', ['', '2325', '2329', '2334', '2338'])
                ->groupBy('texture')
                ->get();
            return response(['status' => 'success', 'data' => $texture]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetTexture Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function GetSkinType()
    {
        try {
            $skin_type = DB::table('products')->selectRaw('skin_type')
                ->whereNotIn('skin_type', [''])
                ->groupBy('skin_type')
                ->get();
            return response(['status' => 'success', 'data' => $skin_type]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetSkinType Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function GetAreaOfApply()
    {
        try {
            $area_of_apply = DB::table('products')->selectRaw('area_of_apply')
                ->whereNotIn('area_of_apply', [''])
                ->groupBy('area_of_apply')
                ->get();
            return response(['status' => 'success', 'data' => $area_of_apply]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'GetAreaOfApply Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

}
