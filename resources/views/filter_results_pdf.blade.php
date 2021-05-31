<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search Results</title>
    <style>
        * {
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            padding: 57px;
            font-size: 16px;
            line-height: 1.6rem;
        }

        /* the-all-content-is */
        .the-all-content-is {
            max-width: 840px;
            margin: 0 auto;
        }

        /* Other */
        .vt {
            vertical-align: top;
        }

        .vc {
            vertical-align: center;
        }

        /* text-align */
        .tr {
            text-align: right;
        }

        .tl {
            text-align: left;
        }

        /* clear both */
        .cb-1 {
            clear: both;
            font-weight: bold;
        }

        .cb-1 {
            font-size: 20px;
        }

        .cb-2 {


            font-size: 11px;
        }

        th.w40.tl.vc * {
            float: left;
        }

        th.w60.tl.vc * {
            float: left;
        }

        th.w70.tl.vc * {
            float: left;
        }

        /* width */
        .w100 {
            width: 100%;
        }

        .w90 {
            width: 90%;
        }

        .w80 {
            width: 80%;
        }

        .w70 {
            width: 70%;
        }

        .w60 {
            width: 60%;
        }

        .w50 {
            width: 50%;
        }

        .w40 {
            width: 40%;
        }

        .w30 {
            width: 30%;
        }

        .w20 {
            width: 20%;
        }

        .w10 {
            width: 10%;
        }

        /* bold */
        b {
            display: inline-block;
        }

        /* paragraph */
        p {
            display: inline-block;
            font-weight: normal;
        }

        /* sapn */
        .span_p {
            font-weight: normal !important;
        }

        /* logo */
        .logo {
            width: 237px;
        }

        /* the-line */
        .the-line {
            display: inline-block;
            height: 5px;
            background: #01aef0;
            width: 100%;
            margin: 25px 0;
        }

        .the-line2 {
            display: inline-block;
            background: #01aef0;
            width: 100%;
        }

        /* the-mean-table */
        .the-mean-table th,
        .the-mean-table td {
            padding: 5px 2px !important;
            text-align: left;
            border: 1px solid #cbcbcb;
            font-size: 12px;
            height: 5%;
        }

        .the-mean-table th {
            background: #01aef0;
            color: #fff;
            border: 1px solid #01aef0;
            white-space: nowrap;
        }

        .the-mean-table {
            border: 1px solid #cbcbcb;
            border-collapse: collapse;
        }

        .the-mean-table tr:nth-child(odd) {
            background-color: #f0f0f0;
        }

        .span_cat {
            color: #e09b34;
            font-weight: 500;
        }

        .the-title-1 {
            text-align: center;
        }

    </style>
</head>

<body>
    <div class="the-title-1">
        <h2>Search Results</h2>
    </div>
    <div class="the-all-content-is">
        <!-- tabel -->
        <table class="w100">
            <tbody>
                @foreach ($results as $index => $item)
                    <tr class="w100">
                        <th class="w100 tr vc" style="border-bottom: 1px solid #dee2e6;">
                            <br>
                            <p class="p_right">
                                <a><strong style="color: #03c;">{{ $item['name'] }}</strong></a>
                                @if (!empty($item['category']))
                                    - <span class="span_cat">{{ $item['category'] }}</span>
                                @endif
                            </p>
                            <br>
                            <p class="p_right dir_rtl">{{ strip_tags($item['description']) }}</p>
                            <br>
                            <p class="p_right">{{ $item['sku'] }} </p>
                        </th>
                    </tr>
                    <br>
                    <!-- tabel -->
                    <table class="w100 the-mean-table" style="margin-bottom: 5%">
                        <tbody>
                            <!-- tr -->
                            <tr>
                                <th>
                                    Onwer
                                </th>
                                <th>
                                    Current Price
                                </th>
                                <th>
                                    After Discount Price
                                </th>
                            </tr>
                            <!-- /tr -->
                            <!-- tr -->
                            <tr>
                                <td>
                                    GoldenScent
                                </td>
                                @if (empty($item['golden']['golden_price']))
                                    <td class="text-center">
                                        Not Found
                                    </td>
                                @else
                                    <td class="text-center">
                                        <span class="green">{{ $item['golden']['golden_price'] }}</span>
                                    </td>
                                @endif

                                @if (empty($item['golden']['golden_discount_price']))
                                    <td class="text-center">
                                        Not Found
                                    </td>
                                @else
                                    <td class="text-center">
                                        <span class="red">{{ $item['golden']['golden_discount_price'] }}</span>
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td>
                                    Dorepha
                                </td>
                                @if (empty($item['dorepha']['dorepha_price']))
                                    <td class="text-center">
                                        Not Found
                                    </td>
                                @else
                                    @if ($item['dorepha']['dorepha_stock'] == 'outofstock')
                                        <td class="text-center">
                                            <span class="out_stock">Out of Stock</span>
                                        </td>
                                    @else
                                        <td class="text-center">
                                            <span class="green">{{ $item['dorepha']['dorepha_price'] }}</span>
                                        </td>
                                    @endif
                                @endif

                                @if (empty($item['dorepha']['dorepha_discount_price']))
                                    <td class="text-center">
                                        Not Found
                                    </td>
                                @else
                                    @if ($item['dorepha']['dorepha_stock'] == 'outofstock')
                                        <td class="text-center">
                                            <span class="out_stock">Out of Stock</span>
                                        </td>
                                    @else
                                        <td class="text-center">
                                            <span class="red">
                                                @if ($item['dorepha']['dorepha_discount_price'] == '0.00' || $item['dorepha']['dorepha_discount_price'] == '0')
                                                    {{ $item['dorepha']['dorepha_price'] }}
                                                @else
                                                    {{ $item['dorepha']['dorepha_discount_price'] }}
                                                @endif
                                            </span>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                            <tr>
                                <td>
                                    Maesta
                                </td>
                                @if (empty($item['maesta']['maesta_price']))
                                    <td class="text-center">
                                        Not Found
                                    </td>
                                @else
                                    <td class="text-center">
                                        <span class="green">{{ $item['maesta']['maesta_price'] }}</span>
                                    </td>
                                @endif

                                @if (empty($item['maesta']['maesta_discount_price']))
                                    <td class="text-center">
                                        Not Found
                                    </td>
                                @else
                                    <td class="text-center">
                                        <span class="red">
                                            @if ($item['maesta']['maesta_discount_price'] == '0.00')
                                                {{ $item['maesta']['maesta_price'] }}
                                            @else
                                                {{ $item['maesta']['maesta_discount_price'] }}
                                            @endif
                                        </span>
                                    </td>
                                @endif
                            </tr>

                        </tbody>
                    </table>
                    <!-- //tabel -->
                @endforeach
            </tbody>
        </table>
        <!-- //tabel -->
        <!-- the-all -->
        <br />
        <!-- // the-all -->

    </div>
</body>

</html>
