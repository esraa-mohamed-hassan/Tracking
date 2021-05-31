<thead class="thead-dark">
<tr>
    <th scope="col"></th>
</tr>
</thead>
<tbody>
@if (count($data) == 0)
    <tr>
        <td colspan="2" style="text-align: center;">No results</td>
    </tr>
@else
    @foreach ($data as $index => $item)
        <tr>
            <td>
            @if (empty($item['golden']['golden_price']))
                    <div class="col-12 column">
                        <p class="data_left"><strong>SKU:</strong> {{ $item['sku'] }}</p>

                        <div class="p_right" style="display: inline-flex;">
                            @if (!empty($item['category']))
                                <form action="#" method="post" id="leads_search"
                                      style="display: inline-block;">
                                    @csrf
                                    <input name="search_categroy_link" type="hidden" value="{{ $item['category'] }}"
                                           id="search_categroy_hidden">
                                    <button type="submit" id="submit_search" style="border: none;background: none;">
                                        <u class="span_cat"><span id="link_category">{{ $item['category'] }}</span></u>
                                        -
                                    </button>
                                </form>
                            @endif
                            <form action="/chart/{{ $item['sku'] }}/one_month" method="post" target="_blank">
                                @csrf
                                <input type="hidden" value="{{json_encode($all_result[$item['sku']])}}"
                                       name="result_by_sku" id="result_by_sku">
                                <button type="submit" style="border: none;background: none;">
                                    <a href="javascript:void(0);">
                                        <strong style="color: #03c;">{{ $item['name'] }}</strong>
                                    </a>
                                </button>
                            </form>
                        </div>
                        <p class="p_right dir_rtl">{{ strip_tags($item['description']) }}</p>
                    </div>
                @else
                    <br>
                    <div class="col-12 column">
                        <p class="data_left"><strong>SKU:</strong> {{ $item['sku'] }}</p>

                        <div class="p_right" style="display: inline-flex;">
                            @if (!empty($item['category']))
                                <form action="#" method="post" id="leads_search"
                                      style="display: inline-block;">
                                    @csrf
                                    <input name="search_categroy_link" type="hidden" value="{{ $item['category'] }}"
                                           id="search_categroy_hidden">
                                    <button type="submit" id="submit_search" style="border: none;background: none;">
                                        <u class="span_cat"><span id="link_category">{{ $item['category'] }}</span></u>
                                        -
                                    </button>
                                </form>
                            @endif
                            <form action="/chart/{{ $item['sku'] }}/one_month" method="post" target="_blank">
                                @csrf
                                <input type="hidden" value="{{json_encode($all_result[$item['sku']])}}"
                                       name="result_by_sku" id="result_by_sku">
                                <button type="submit" style="border: none;background: none;">
                                    <a href="javascript:void(0);">
                                        <strong style="color: #03c;">{{ $item['name'] }}</strong>
                                    </a>
                                </button>
                            </form>
                        </div>
                        <p class="p_right dir_rtl">{{ explode('p>', strip_tags($item['description']))[0] }}</p>
                    </div>
                @endif

                <div class="col-12 data_left">

                    <table>
                        <tbody>
                        <tr>
                            <td class="first_watch_header"> Onwer
                            <th class="text-center watch_row price0">GoldenScent</th>
                            <th class="text-center watch_row price1">Dorepha</th>
                            <th class="text-center watch_row price2">Maesta</th>
                            <th class="text-center watch_row price3">NiceOne</th>
                            </td>
                        </tr>
                        <tr>
                            <?php
                            $price_golden_update = App\UpdateGoldenScentPricesForProducts::where('sku', $item['sku'])->first();
                            $price_dorepha_update = App\UpdateDorephsPricesForProducts::where('sku', $item['sku'])->first();
                            $price_maesta_update = App\UpdateMaestaPricesForProducts::where('sku', $item['sku'])->first();
                            $price_niceone_update = App\UpdateNiceonePricesForProducts::where('sku', $item['sku'])->first();
                            ?>
                            <td>
                                Current Price
                            @if (empty($item['golden']['golden_price']) && empty($price_golden_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['golden']['golden_price']) && !empty($price_golden_update))
                                <td class="text-center">
                                    Not Available
                                </td>
                            @else
                                <td class="text-center">
                                    <span class="green">{{ $item['golden']['golden_price'] }}</span>
                                </td>
                            @endif

                            @if (empty($item['dorepha']['dorepha_price']) && empty($price_dorepha_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['dorepha']['dorepha_price']) && !empty($price_dorepha_update))
                                <td class="text-center">
                                    Not Available
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

                            @if (empty($item['maesta']['maesta_price']) && empty($price_maesta_update))
                                <td class="text-center">
                                    Not Found
                                </td>

                            @elseif (empty($item['maesta']['maesta_price']) && !empty($price_maesta_update))
                                <td class="text-center">
                                    Not Available
                                </td>
                            @else
                                <td class="text-center">
                                    <span class="green">{{ $item['maesta']['maesta_price'] }}</span>
                                </td>
                            @endif

                            @if (empty($item['niceone']['niceone_price']) && empty($price_niceone_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['niceone']['niceone_price']) && !empty($price_niceone_update))
                                <td class="text-center">
                                    Not Available
                                </td>
                            @else
                                <td class="text-center">
                                    <span class="green">{{ $item['niceone']['niceone_price'] }}</span>
                                </td>
                            @endif
                           </td>
                        </tr>
                        <tr>
                            <td>
                                After Discount Price
                            @if (empty($item['golden']['golden_discount_price']) && empty($price_golden_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['golden']['golden_discount_price']) && !empty($price_golden_update))
                                <td class="text-center">
                                    Not Available
                                </td>
                            @else
                                <td class="text-center">
                                    <span class="red">
                                        @if ($item['golden']['golden_discount_price'] == '0.00' || $item['golden']['golden_discount_price'] == '0')
                                            {{ $item['golden']['golden_price'] }}
                                        @else
                                            {{ $item['golden']['golden_discount_price'] }}
                                        @endif
                                        </span>
                                </td>
                            @endif
                            @if (empty($item['dorepha']['dorepha_discount_price']) && empty($price_dorepha_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['dorepha']['dorepha_discount_price']) && !empty($price_dorepha_update))
                                <td class="text-center">
                                    Not Available
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

                            @if (empty($item['maesta']['maesta_discount_price']) && empty($price_maesta_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['maesta']['maesta_discount_price']) && !empty($price_maesta_update))
                                <td class="text-center">
                                    Not Available
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


                            @if (empty($item['niceone']['niceone_discount_price']) && empty($price_niceone_update))
                                <td class="text-center">
                                    Not Found
                                </td>
                            @elseif (empty($item['niceone']['niceone_discount_price']) && !empty($price_niceone_update))
                                <td class="text-center">
                                    Not Available
                                </td>
                            @else
                                <td class="text-center">
                                    <span class="red">
                                        @if ($item['niceone']['niceone_discount_price'] == '0.00')
                                            {{ $item['niceone']['niceone_price'] }}
                                        @else
                                            {{ $item['niceone']['niceone_discount_price'] }}
                                        @endif
                                    </span>
                                </td>
                             @endif
                         </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
@endforeach
@endif
