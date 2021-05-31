<!doctype html>
<html lang="en">

<head>
    <title>Chart History</title>
    @include('layouts/header')
    <style>
        .table th,
        .table td {
            border: none;
        }

    </style>
</head>
<body class="open-menu-1">

<div id="overlay">
    <div class="overlay__inner">
        <img src="{{env('APP_URL')}}/asset/images/processing.gif?v=1">
    </div>
</div>
<!-- the-top-header-1 -->
@include('layouts/menu')
<!-- the-top-header-1 // -->

<div class="sidenav">
    <div class="site_name">
        <h4>{{ config('app.name') }}</h4>
    </div>
    <a href="/search" class="input_active">Search</a>
    <a href="#">Profile</a>
    @if (\Illuminate\Support\Facades\Auth::user()->role == 'admin')
        <button class="dropdown-btn input_active">User Mangemant
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-container">
            <a href="/user_management" id="user_mang">User Mangemant</a>
            <a href="/add_user" id="add_new_user">Add user</a>
        </div>
    @endif
</div>

<!-- the-table-content-1 -->
<div class="the-table-content-1 my-0 d-block overflow-auto pb-5 mb-5">
    <!-- container -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mt-3">
                <form action="/searching" method="post">
                    @csrf
                    <div class="input-group">
                            <span class="input-group-prepend">
                                <div class="input-group-text bg-transparent border-right-0"><i class="fa fa-search"></i>
                                </div>
                            </span>
                        <input class="form-control py-2 border-left-0 border" name="search" type="search" value=""
                               placeholder="Search by Name or SKU" id="search-input" required>
                        <span class="input-group-append">
                                <button class="btn btn btn-primary border-left-0 border" type="submit">
                                    Search
                                </button>
                            </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12" style="margin-top: 2%;">
                <div class="col-10 column">

                    <h1 class="p_right text_bold"><?php echo $data['data']['name']; ?></h1>
                    <p class="p_right dir_rtl"><?php echo explode('p>', strip_tags($data['data']['description']))[0];
                        ?></p>
                </div>
                <div class="col-2 data_left">
                    <h1 class="dir_rtl">
                            <span class="green" style="font-size: 22px;font-weight: 600;">{{ $data['data']['price'] }}
                                ر.س </span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="row date_rang">
            <div class="col-2 data_left">
                <p class="text_bold">Date range</p>
            </div>
            <div class="col-10 column">
                <div class="form-check form-check-inline">
                    <input class="form-check-input active" type="radio" checked name="inlineRadioOptions" onclick=""
                           id="#one_month" value="one_month">
                    <label class="form-check-label text_bold" for="inlineRadio1">1 Month</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input three_months" type="radio" name="inlineRadioOptions" id="#three_months"
                           value="three_months">
                    <label class="form-check-label text_bold" for="inlineRadio2">3 Months</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="#six_months"
                           value="six_months">
                    <label class="form-check-label text_bold" for="inlineRadio3">6 Months</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="#one_year"
                           value="one_year">
                    <label class="form-check-label text_bold" for="inlineRadio4">1 year</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="#all" value="all">
                    <label class="form-check-label text_bold" for="inlineRadio5">All</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="chart_border" id="draw_chart">
                    {{ $chart->container() }}
                </div>
            </div>
        </div>

        <div class="row date_rang">
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="third_col">
                    <p class="p_first_col"><span class="span_third_col"></span>GoldenScent</p>
                    <p class="p_text_size"> This is the price charged for New products when GoldenScent itself is the seller.</p>


                    <table class="table table-striped" id="goldenscent_table">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Type</th>
                            <th scope="col">Price</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($golden_prices) == 0)
                            <tr>
                                <td colspan="3" style="text-align: center;">Sorry, we have no data for this
                                    type.</td>
                            </tr>
                        @else
                            <tr>
                                <td>Current</td>
                                <td class="price_rtl"> {{ $golden_prices[0]['current'] }} ر.س</td>
                                <td>{{ $golden_prices[0]['current_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="highest_price">Highest</td>
                                <td class="price_rtl highest_price"> {{ $golden_prices[0]['max'] }} ر.س</td>
                                <td class="highest_price">{{ $golden_prices[0]['max_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="lowest_price">Lowest</td>
                                <td class="price_rtl lowest_price"> {{ $golden_prices[0]['min'] }} ر.س</td>
                                <td class="lowest_price">{{ $golden_prices[0]['min_date'] }}</td>
                            </tr>
                            <tr>
                                <td>Average</td>
                                <td class="price_rtl"> {{ $golden_prices[0]['avg'] }} ر.س</td>
                                <td>-</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="first_col">
                    <p class="p_first_col"><span class="span_first_col"></span>Dorepha</p>
                    <p class="p_text_size"> This is the price charged for New products when Dorepha itself is the seller.</p>

                    <table class="table table-striped" id="dorepha_table">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Type</th>
                            <th scope="col">Price</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($dorepha_prices) == 0)
                            <tr>
                                <td colspan="3" style="text-align: center;">Sorry, we have no data for this
                                    type.</td>
                            </tr>
                        @else
                            <tr>
                                <td>Current</td>
                                <td class="price_rtl"> {{ $dorepha_prices[0]['current'] }} ر.س</td>
                                <td>{{ $dorepha_prices[0]['current_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="highest_price">Highest</td>
                                <td class="price_rtl highest_price"> {{ $dorepha_prices[0]['max'] }} ر.س</td>
                                <td class="highest_price">{{ $dorepha_prices[0]['max_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="lowest_price">Lowest</td>
                                <td class="price_rtl lowest_price"> {{ $dorepha_prices[0]['min'] }} ر.س</td>
                                <td class="lowest_price">{{ $dorepha_prices[0]['min_date'] }}</td>
                            </tr>
                            <tr>
                                <td>Average</td>
                                <td class="price_rtl"> {{ $dorepha_prices[0]['avg'] }} ر.س</td>
                                <td>-</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="second_col">
                    <p class="p_first_col"><span class="span_second_col"></span>Maesta</p>
                    <p class="p_text_size"> This is the price charged for New products when Maesta itself is the seller.</p>


                    <table class="table table-striped" id="maesta_table">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Type</th>
                            <th scope="col">Price</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($maesta_prices) == 0)
                            <tr>
                                <td colspan="3" style="text-align: center;">Sorry, we have no data for this
                                    type.</td>
                            </tr>
                        @else
                            <tr>
                                <td>Current</td>
                                <td class="price_rtl"> {{ $maesta_prices[0]['current'] }} ر.س</td>
                                <td>{{ $maesta_prices[0]['current_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="highest_price">Highest</td>
                                <td class="price_rtl highest_price"> {{ $maesta_prices[0]['max'] }} ر.س</td>
                                <td class="highest_price">{{ $maesta_prices[0]['max_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="lowest_price">Lowest</td>
                                <td class="price_rtl lowest_price"> {{ $maesta_prices[0]['min'] }} ر.س</td>
                                <td class="lowest_price">{{ $maesta_prices[0]['min_date'] }}</td>
                            </tr>
                            <tr>
                                <td>Average</td>
                                <td class="price_rtl"> {{ $maesta_prices[0]['avg'] }} ر.س</td>
                                <td>-</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="second_col">
                    <p class="p_first_col"><span class="span_third_col"></span>NiceOne</p>
                    <p class="p_text_size"> This is the price charged for New products when NiceOne itself is the seller.</p>


                    <table class="table table-striped" id="maesta_table">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Type</th>
                            <th scope="col">Price</th>
                            <th scope="col">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($niceone_prices) == 0)
                            <tr>
                                <td colspan="3" style="text-align: center;">Sorry, we have no data for this
                                    type.</td>
                            </tr>
                        @else
                            <tr>
                                <td>Current</td>
                                <td class="price_rtl"> {{ $niceone_prices[0]['current'] }} ر.س</td>
                                <td>{{ $niceone_prices[0]['current_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="highest_price">Highest</td>
                                <td class="price_rtl highest_price"> {{ $niceone_prices[0]['max'] }} ر.س</td>
                                <td class="highest_price">{{ $niceone_prices[0]['max_date'] }}</td>
                            </tr>
                            <tr>
                                <td class="lowest_price">Lowest</td>
                                <td class="price_rtl lowest_price"> {{ $niceone_prices[0]['min'] }} ر.س</td>
                                <td class="lowest_price">{{ $niceone_prices[0]['min_date'] }}</td>
                            </tr>
                            <tr>
                                <td>Average</td>
                                <td class="price_rtl"> {{ $niceone_prices[0]['avg'] }} ر.س</td>
                                <td>-</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table class="table table-striped" id="product_details_table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col" colspan="2"><i class="fa fa-info-circle" aria-hidden="true"></i> Product
                            Details
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="W_15">Name</td>
                        <td class="dir_rtl p_right"><?php echo $data['data']['name']; ?></td>
                    </tr>
                    <tr>
                        <td class="W_15">SKU</td>
                        <td class="dir_rtl p_right" id="sku"><?php echo $data['data']['sku']; ?></td>
                    </tr>
                    @if (!empty($data['data']['category']))
                        <tr>
                            <td class="W_15">Category</td>
                            <td class="dir_rtl p_right">{{ $data['data']['category'] }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="W_15">Description</td>
                        <td class="dir_rtl p_right"><?php
                            echo explode('p>', strip_tags($data['data']['description']))[0]; ?></td>
                    </tr>
                    @if (!empty($data['data']['brand_value']))
                        <tr>
                            <td class="W_15">Brand</td>
                            <td class="dir_rtl p_right">{{ $data['data']['brand_value'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($data['data']['golden']['concentration']))
                        <tr>
                            <td class="W_15">Concentration</td>
                            <td class="dir_rtl p_right">{{ $data['data']['golden']['concentration'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($data['data']['size']))
                        <tr>
                            <td class="W_15">Size</td>
                            <td class="dir_rtl p_right">{{ $data['data']['size'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($data['data']['golden']['color']))
                        <tr>
                            <td class="W_15">Color</td>
                            <td class="dir_rtl p_right">{{ $data['data']['golden']['color'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($data['data']['golden']['texture']))
                        <tr>
                            <td class="W_15">Formulation</td>
                            <td class="dir_rtl p_right">{{ $data['data']['golden']['texture'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($data['data']['golden']['skin_type']))
                        <tr>
                            <td class="W_15">Skin Type</td>
                            <td class="dir_rtl p_right">{{ $data['data']['golden']['skin_type'] }}</td>
                        </tr>
                    @endif
                    @if (!empty($data['data']['area_of_apply']))
                        <tr>
                            <td class="W_15">Area of Application</td>
                            <td class="dir_rtl p_right">{{ $data['data']['area_of_apply'] }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<script src="{{ $chart->cdn() }}"></script>

{{ $chart->script() }}

@include('layouts/footer')

<script src="{{env('APP_URL')}}/asset/js/chart.js"></script>
</body>

</html>
