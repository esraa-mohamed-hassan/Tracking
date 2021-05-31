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

class ChartsController extends Controller
{
    public function __construct()
    {
        ini_set('max_execution_time', 0);

        $this->middleware('auth');
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

    public function getDataGoldenUpdatePrice($sku, $fromDate)
    {
        try {
            $golden_prices_pro = DB::table('update_goldenscent_price as golden')
                ->select('sku', 'price', 'golden.price_after_discount as discount_price', 'created_at',
                    DB::raw("'golden' as 'table_name'")
                )->where('golden.sku', $sku)->where('golden.created_at', '>=', $fromDate);
            return $golden_prices_pro;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'getDataGoldenUpdatePrice Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function getDataDorephsUpdatePrice($sku, $fromDate)
    {
        try {
            $dorepha_prices_pro = DB::table('update_dorepha_price as dorepha')
                ->select('sku', 'price', 'dorepha.price_after_discount as discount_price', 'created_at',
                    DB::raw("'dorepha' as 'table_name'")
                )->where('dorepha.sku', 'like', '%' . $sku . '%')->where('dorepha.created_at', '>=', $fromDate);
            return $dorepha_prices_pro;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'getDataDorephsUpdatePrice Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function getDataMaestaUpdatePrice($sku, $fromDate)
    {
        try {
            $maesta_prices_pro = DB::table('update_maesta_price as maesta')
                ->select('sku', 'price', 'maesta.price_after_discount as discount_price', 'created_at',
                    DB::raw("'maesta' as 'table_name'")
                )->where('maesta.sku', 'like', '%' . $sku . '%')->where('maesta.created_at', '>=', $fromDate);
            return $maesta_prices_pro;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'getDataMaestaUpdatePrice Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function getDataNiceoneUpdatePrice($sku, $fromDate)
    {
        try {
            $niceone_prices_pro = DB::table('update_niceone_price as niceone')
                ->select('sku', 'price', 'niceone.price_after_discount as discount_price', 'created_at',
                    DB::raw("'niceone' as 'table_name'")
                )->where('niceone.sku', 'like', '%' . $sku . '%')->where('niceone.created_at', '>=', $fromDate);
            return $niceone_prices_pro;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'getDataNiceoneUpdatePrice Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function ResultChartData($data_results, $sku, $filter, $fromDate)
    {
        try {
            $time = [];
            $dorepha_data_chart = [];
            $maesta_data_chart = [];
            $golden_data_chart = [];
            $niceone_data_chart = [];

            $all_result = $data_results;

            $golden_prices_pro = $this->getDataGoldenUpdatePrice($sku, $fromDate);
            $dorepha_prices_pro = $this->getDataDorephsUpdatePrice($sku, $fromDate);
            $maesta_prices_pro = $this->getDataMaestaUpdatePrice($sku, $fromDate);
            $niceone_prices_pro = $this->getDataNiceoneUpdatePrice($sku, $fromDate);

            $union_data = $maesta_prices_pro->union($niceone_prices_pro)->union($dorepha_prices_pro)->union($golden_prices_pro);
            $results = $union_data->orderBy('created_at', 'asc')->get();
            foreach ($results as $res) {
                if ($filter == 'all') {
                    $date = date('d M Y', strtotime($res->created_at));
                } else {
                    $date = date('d M', strtotime($res->created_at));
                }

                if ($res->table_name == 'dorepha') {
                    $dorepha_data_chart[$date] = [];
                    if ($res->discount_price == '0' || $res->discount_price == '0.00') {
                        $pricinng = $res->price;
                    } else {
                        $pricinng = $res->discount_price;
                    }
                    array_push($dorepha_data_chart[$date], $pricinng);

                } elseif ($res->table_name == 'maesta') {
                    $maesta_data_chart[$date] = [];
                    if ($res->discount_price == '0' || $res->discount_price == '0.00') {
                        $pricinng = $res->price;
                    } else {
                        $pricinng = $res->discount_price;
                    }
                    array_push($maesta_data_chart[$date], $pricinng);

                } elseif ($res->table_name == 'golden') {
                    $golden_data_chart[$date] = [];
                    if ($res->discount_price == '0' || $res->discount_price == '0.00') {
                        $pricinng = $res->price;
                    } else {
                        $pricinng = $res->discount_price;
                    }
                    array_push($golden_data_chart[$date], $pricinng);

                } elseif ($res->table_name == 'niceone') {
                    $niceone_data_chart[$date] = [];
                    if ($res->discount_price == '0' || $res->discount_price == '0.00') {
                        $pricinng = $res->price;
                    } else {
                        $pricinng = $res->discount_price;
                    }
                    array_push($niceone_data_chart[$date], $pricinng);
                }

                if (!in_array($date, $time)) {
                    array_push($time, $date);
                }
            }
            return [
                'all_result' => $all_result,
                'golden_data_chart' => $golden_data_chart,
                'dorepha_data_chart' => $dorepha_data_chart,
                'maesta_data_chart' => $maesta_data_chart,
                'niceone_data_chart' => $niceone_data_chart,
                'time' => $time,
            ];
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'ResultChartData Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function getDatafromDBWithFiters($sku, $filter, $results)
    {
        try {
            $dataset = [];

            $dorepha_prices = [];
            $maesta_prices = [];
            $niceone_prices = [];
            $golden_prices = [];
            $dorepha = [];
            $maesta = [];
            $goldenscent = [];
            $niceone = [];
            $fromDate = '';

            if ($filter == 'one_month') {
                $fromDate = Carbon::now()->subDays(30)->toDateTimeString(); //1 month

            } elseif ($filter == 'three_months') {
                $fromDate = Carbon::now()->submonths(3)->toDateTimeString(); //3 months

            } elseif ($filter == 'six_months') {
                $fromDate = Carbon::now()->submonths(6)->toDateTimeString(); //6 months

            } elseif ($filter == 'one_year') {
                $fromDate = Carbon::now()->addYears(-1)->toDateTimeString(); //1 year
            }

            $res = $this->ResultChartData($results, $sku, $filter, $fromDate);
            $data = $res['all_result'];
            $time = $res['time'];

            $golden_data_chart = $res['golden_data_chart'];
            $dorepha_data_chart = $res['dorepha_data_chart'];
            $maesta_data_chart = $res['maesta_data_chart'];
            $niceone_data_chart = $res['niceone_data_chart'];

            $dorepha_max = UpdateDorephsPricesForProducts::where('sku', 'like', '%' . $sku . '%')->max('price');
            $dorepha_price_min = UpdateDorephsPricesForProducts::where('sku', 'like', '%' . $sku . '%')->min('price');
            $dorepha_discount_price_min = UpdateDorephsPricesForProducts::where('sku', 'like', '%' . $sku . '%')->where('price_after_discount', '!=', '0.00')->min('price_after_discount');
            if (!empty($dorepha_discount_price_min)) {
                $dorepha_min = min(compact('dorepha_price_min', 'dorepha_discount_price_min'));
            } else {
                $dorepha_min = $dorepha_price_min;
            }
            $dorepha_current = UpdateDorephsPricesForProducts::select('sku', 'price', 'price_after_discount as discount_price', 'created_at')->where('sku', 'like', '%' . $sku . '%')->latest('created_at')->first();
            $dorepha_max_date = UpdateDorephsPricesForProducts::select('created_at')->where('sku', 'like', '%' . $sku . '%')->where('price', $dorepha_max)->first();
            $dorepha_min_date = UpdateDorephsPricesForProducts::select('created_at')->where('sku', 'like', '%' . $sku . '%')->Where(function ($dorepha_min_date) use ($dorepha_min) {
                $dorepha_min_date->where('price', $dorepha_min);
                $dorepha_min_date->orwhere('price_after_discount', $dorepha_min);
            })->first();

            if (!empty($dorepha_current) && !empty($dorepha_max_date) && !empty($dorepha_min_date)) {
                $dorepha_avg = ($dorepha_max + $dorepha_min + $dorepha_current->price) / 3;
                array_push($dorepha_prices, [
                    'current' => $dorepha_current->price,
                    'current_date' => date('Y-m-d', strtotime($dorepha_current->created_at)),
                    'max' => $dorepha_max,
                    'max_date' => date('Y-m-d', strtotime($dorepha_max_date->created_at)),
                    'min' => $dorepha_min,
                    'min_date' => date('Y-m-d', strtotime($dorepha_min_date->created_at)),
                    'avg' => number_format((float)$dorepha_avg, 2, '.', ''),
                ]);
            }

            $golden_max = UpdateGoldenScentPricesForProducts::where('sku', $sku)->max('price');
            $golden_price_min = UpdateGoldenScentPricesForProducts::where('sku', $sku)->min('price');
            $golden_discount_price_min = UpdateGoldenScentPricesForProducts::where('sku', $sku)->where('price_after_discount', '!=', '0.00')->min('price_after_discount');
            if (!empty($golden_discount_price_min)) {
                $golden_min = min(compact('golden_price_min', 'golden_discount_price_min'));
            } else {
                $golden_min = $golden_price_min;
            }
            $golden_current = UpdateGoldenScentPricesForProducts::select('*')->where('sku', $sku)->latest('created_at')->first();
            $golden_max_date = UpdateGoldenScentPricesForProducts::select('created_at')->where('sku', $sku)->where('price', $golden_max)->first();
            $golden_min_date = UpdateGoldenScentPricesForProducts::select('*')->where('sku', $sku)->Where(function ($golden_min_date) use ($golden_min) {
                $golden_min_date->where('price', $golden_min);
                $golden_min_date->orwhere('price_after_discount', $golden_min);
            })->first();

            if (!empty($golden_current) && !empty($golden_max_date) && !empty($golden_min_date)) {
                $golden_avg = ($golden_max + $golden_min + $golden_current->price) / 3;
                array_push($golden_prices, [
                    'current' => $golden_current->price,
                    'current_date' => date('Y-m-d', strtotime($golden_current->created_at)),
                    'max' => $golden_max,
                    'max_date' => date('Y-m-d', strtotime($golden_max_date->created_at)),
                    'min' => $golden_min,
                    'min_date' => date('Y-m-d', strtotime($golden_min_date->created_at)),
                    'avg' => number_format((float)$golden_avg, 2, '.', ''),

                ]);
            }

            $maesta_max = UpdateMaestaPricesForProducts::where('sku', 'like', '%' . $sku . '%')->max('price');
            $maesta_price_min = UpdateMaestaPricesForProducts::where('sku', 'like', '%' . $sku . '%')->min('price');
            $maesta_discount_price_min = UpdateMaestaPricesForProducts::where('sku', 'like', '%' . $sku . '%')->where('price_after_discount', '!=', '0.00')->min('price_after_discount');
            if (!empty($maesta_discount_price_min)) {
                $maesta_min = min(compact('maesta_price_min', 'maesta_discount_price_min'));
            } else {
                $maesta_min = $maesta_price_min;
            }
            $maesta_current = UpdateMaestaPricesForProducts::select('*')->where('sku', 'like', '%' . $sku . '%')->latest('created_at')->first();
            $maesta_max_date = UpdateMaestaPricesForProducts::select('created_at')->where('sku', 'like', '%' . $sku . '%')->where('price', $maesta_max)->first();
            $maesta_min_date = UpdateMaestaPricesForProducts::select('created_at')->where('sku', 'like', '%' . $sku . '%')->Where(function ($maesta_min_date) use ($maesta_min) {
                $maesta_min_date->where('price', $maesta_min);
                $maesta_min_date->orwhere('price_after_discount', $maesta_min);
            })->first();

            if (!empty($maesta_current) && !empty($maesta_max_date) && !empty($maesta_min_date)) {
                $maesta_avg = ($maesta_max + $maesta_min + $maesta_current->price) / 3;
                array_push($maesta_prices, [
                    'current' => $maesta_current->price,
                    'current_date' => date('Y-m-d', strtotime($maesta_current->created_at)),
                    'max' => $maesta_max,
                    'max_date' => date('Y-m-d', strtotime($maesta_max_date->created_at)),
                    'min' => $maesta_min,
                    'min_date' => date('Y-m-d', strtotime($maesta_min_date->created_at)),
                    'avg' => number_format((float)$maesta_avg, 2, '.', ''),
                ]);
            }


            $niceone_max = UpdateNiceonePricesForProducts::where('sku', 'like', '%' . $sku . '%')->max('price');
            $niceone_price_min = UpdateNiceonePricesForProducts::where('sku', 'like', '%' . $sku . '%')->min('price');
            $niceone_discount_price_min = UpdateNiceonePricesForProducts::where('sku', 'like', '%' . $sku . '%')->where('price_after_discount', '!=', '0.00')->min('price_after_discount');
            if (!empty($niceone_discount_price_min)) {
                $niceone_min = min(compact('niceone_price_min', 'niceone_discount_price_min'));
            } else {
                $niceone_min = $niceone_price_min;
            }
            $niceone_current = UpdateNiceonePricesForProducts::select('*')->where('sku', 'like', '%' . $sku . '%')->latest('created_at')->first();
            $niceone_max_date = UpdateNiceonePricesForProducts::select('created_at')->where('sku', 'like', '%' . $sku . '%')->where('price', $niceone_max)->first();
            $niceone_min_date = UpdateNiceonePricesForProducts::select('created_at')->where('sku', 'like', '%' . $sku . '%')->Where(function ($niceone_min_date) use ($niceone_min) {
                $niceone_min_date->where('price', $niceone_min);
                $niceone_min_date->orwhere('price_after_discount', $niceone_min);
            })->first();

            if (!empty($niceone_current) && !empty($niceone_max_date) && !empty($niceone_min_date)) {
                $niceone_avg = ($niceone_max + $niceone_min + $niceone_current->price) / 3;
                array_push($niceone_prices, [
                    'current' => $niceone_current->price,
                    'current_date' => date('Y-m-d', strtotime($niceone_current->created_at)),
                    'max' => $niceone_max,
                    'max_date' => date('Y-m-d', strtotime($niceone_max_date->created_at)),
                    'min' => $niceone_min,
                    'min_date' => date('Y-m-d', strtotime($niceone_min_date->created_at)),
                    'avg' => number_format((float)$niceone_avg, 2, '.', ''),
                ]);
            }


            array_push($dataset, [
                'name' => 'Dorepha',
                'data' => $dorepha_data_chart,
            ]);

            array_push($dataset, [
                'name' => 'Maesta',
                'data' => $maesta_data_chart,
            ]);

            array_push($dataset, [
                'name' => 'GoldenScent',
                'data' => $golden_data_chart,
            ]);

            array_push($dataset, [
                'name' => 'NiceOne',
                'data' => $niceone_data_chart,
            ]);


            foreach ($dataset as $item) {
                if ($item['name'] == 'Dorepha') {
                    foreach ($time as $t) {
                        if (array_key_exists($t, $item['data'])) {
                            $item_price = $item['data'][$t][0];
                            array_push($dorepha, $item_price);
                        } else {
                            array_push($dorepha, 0);
                        }
                    }
                } elseif ($item['name'] == 'Maesta') {
                    foreach ($time as $t) {
                        if (array_key_exists($t, $item['data'])) {
                            $item_price = $item['data'][$t][0];
                            array_push($maesta, $item_price);
                        } else {
                            array_push($maesta, 0);
                        }
                    }
                } elseif ($item['name'] == 'GoldenScent') {
                    foreach ($time as $t) {
                        if (array_key_exists($t, $item['data'])) {
                            $item_price = $item['data'][$t][0];
                            array_push($goldenscent, $item_price);
                        } else {
                            array_push($goldenscent, 0);
                        }
                    }
                } elseif ($item['name'] == 'NiceOne') {
                    foreach ($time as $t) {
                        if (array_key_exists($t, $item['data'])) {
                            $item_price = $item['data'][$t][0];
                            array_push($niceone, $item_price);
                        } else {
                            array_push($niceone, 0);
                        }
                    }
                }
            }
            $new_dateset = [];
            array_push($new_dateset,
                [
                    'name' => 'Dorepha',
                    'data' => $dorepha,
                ],
                [
                    'name' => 'Maesta',
                    'data' => $maesta,
                ],
                [
                    'name' => 'GoldenScent',
                    'data' => $goldenscent,
                ],
                [
                    'name' => 'NiceOne',
                    'data' => $niceone,
                ]
            );
            $response = [
                'time' => $time,
                'dataset' => $new_dateset,
                'data' => $data,
                'dorepha_prices' => $dorepha_prices,
                'maesta_prices' => $maesta_prices,
                'golden_prices' => $golden_prices,
                'niceone_prices' => $niceone_prices,
            ];

            return $response;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'getDatafromDBWithFiters Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function Chart($sku, $filter, Request $request)
    {
        try {
            $results = json_decode($request->input('result_by_sku'), true);

            $data = $this->getDatafromDBWithFiters($sku, $filter, $results);

            $chart = (new LarapexChart)->setType('line')
                ->setHeight('400')
                ->setTitle('Prices')
                ->setXAxis($data['time'])
                ->setLabels(['Dorepha', 'Maesta', 'GoldenScent', 'NiceOne'])
                ->setColors(['#ff6384', '#ffc63b', '#007bff', '#80effe'])
                ->setDataset($data['dataset'])
                ->setGrid(true);

            return view('chart')
                ->with('chart', $chart)
                ->with('data', $data)
                ->with('dorepha_prices', $data['dorepha_prices'])
                ->with('maesta_prices', $data['maesta_prices'])
                ->with('niceone_prices', $data['niceone_prices'])
                ->with('golden_prices', $data['golden_prices']);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'Chart Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function ChartWithFilter(Request $request)
    {
        try {
            $sku = $_GET['sku'];
            $filter = $_GET['filter'];
            $data = $this->getDatafromDBWithFiters($sku, $filter, '', '', '', '');
            return $data;
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'ChartWithFilter Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function anyData(Request $request)
    {
        try {
            $page = $request->input('page');
            if ($request->session()->has('results_from_search')) {
                $all_result = $request->session()->get('results_from_search');
                $data = $this->paginate($all_result, 10, $page, '/searching#', []);
                $html_view = view("search_table", compact('data', 'all_result'))->render();
            }
            return response()->json(['html' => $html_view]);
        } catch (\Exception $e) {
            $log = new FiltersLogs();
            $log->FiltersLogsData(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'anyData Error', var_export($e->getMessage(), true), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }
}
