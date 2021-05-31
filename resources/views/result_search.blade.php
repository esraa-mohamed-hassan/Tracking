<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ config('app.name', 'Dorepha & Maesta') }} - Search History </title>
    @include('layouts/header')
    <style>
        table.dataTable td, table.dataTable th {
            border: 1px solid #dee2e6;
        }

        .bootstrap-select:not([class*=col-]):not([class*=form-control]):not(.input-group-btn) {
            width: 100%;
        }


        .the-w-1 .dropdown .dropdown-menu.show {
            display: flex !important;
            flex-direction: column;
        }

        .the-w-1 .dropdown .dropdown-menu ul li {
            order: 1;
        }

        .the-w-1 .dropdown .dropdown-menu ul li.selected {
            order: 0;
        }

        .the-w-1 .dropdown-menu .bs-actionsbox button.bs-select-all {
            display: none !important;
        }

        .the-w-1 .dropdown-menu .bs-actionsbox button.bs-deselect-all {
            width: 100% !important;
            margin-top: 1%;
        }
    </style>
</head>

<body class="open-menu-1">
<div id="overlay">
    <div class="overlay__inner">
        <img src="./asset/images/processing.gif?v=1">
    </div>
</div>

@include('layouts/menu')
<!-- the-table-content-1 -->
<div class="the-table-content-1 my-0 d-block overflow-auto pb-5 mb-5">
    <!-- container -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 mt-3">
                <form action="/searching" method="post" id="leads_search">
                    @csrf
                    <div class="input-group">
                            <span class="input-group-prepend">
                                <div class="input-group-text bg-transparent border-right-0"><i class="fa fa-search"></i>
                                </div>
                            </span>
                        <input class="form-control py-2 border-left-0 border" name="search" type="search"
                               value="{{ isset($_REQUEST['search']) ? $_REQUEST['search'] : '' }}" id="search_input"
                               placeholder="Search by Name or SKU">

                        <span class="input-group-append">
                                <button class="btn btn btn-primary border-left-0 border" type="submit"
                                        id="submit_search">
                                    Filter
                                </button>
                            </span>
                    </div>

                    <!-- Results Search -->
                    <div class="h4 mt-4 mb-1">Search Results</div>

                    <input name="search_categroy_hidden" type="hidden"
                           value="{{ isset($_REQUEST['search_categroy']) ? json_encode($_REQUEST['search_categroy']) : '[]' }}"
                           id="search_categroy_hidden">
                    <input name="search_brand_hidden" type="hidden"
                           value="{{ isset($_REQUEST['search_brand']) ? json_encode($_REQUEST['search_brand']) : '[]' }}"
                           id="search_brand_hidden">
                    <input name="search_attr_hidden" type="hidden"
                           value="{{ isset($_REQUEST['search_attr']) ? $_REQUEST['search_attr'] : '' }}"
                           id="search_attr_hidden">
                    <input name="search_selection_attr_hidden" type="hidden"
                           value="{{ isset($_REQUEST['search_selection_attr']) ? json_encode($_REQUEST['search_selection_attr']) : '[]' }}"
                           id="search_selection_attr_hidden">


                    <div class="form-row">
                        @if(!empty($categories))
                        <div class="form-group col the-w-1" id="show_selection_categroy">
                            <select class="selectpicker show-tick" data-live-search="true"
                                    data-selected-text-format="count" data-actions-box="true" data-max-options="5"
                                    name="search_categroy[]" onchange="CheckEmpty()" id="search_categroy" multiple
                                    title="Select Categroy">
                                @foreach ($categories as $category)
                                    <option class="p_right dir_rtl" data-tokens="{{ $category }}"
                                            value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        @if(!empty($brands))
                        <div class="form-group col the-w-1" id="show_selection_brand">
                            <select class="selectpicker show-tick" data-live-search="true" name="search_brand[]"
                                    data-selected-text-format="count" data-actions-box="true" data-max-options="5"
                                    onchange="CheckEmpty()" id="search_brand" multiple title="Select Brand">
                                @foreach ($brands as $brand)
                                    <option class="p_right dir_rtl" data-tokens="{{ $brand }}"
                                            value="{{ $brand }}">{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>
                            @endif

                        @if(!empty($attributes))
                            <div class="form-group col the-w-1">
                                <select class="selectpicker show-tick" data-live-search="true" name="search_attr"
                                        onchange="ChangeAttr(this.value),CheckEmpty()" id="search_attr"
                                        title="Select Attributes">
                                    @foreach ($attributes as $key => $attribute)
                                        <option class="p_right dir_rtl" data-tokens="{{ $key }}"
                                                value="{{$key}}">
                                            {{ $attribute}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if(!empty($concentrations))
                                <div class="form-group col the-w-1" id="show_selection_attr_concentration" style="display: none;">
                                    <select class="selectpicker show-tick" data-selected-text-format="count"
                                            data-max-options="5" data-live-search="true" data-actions-box="true"
                                            name="search_selection_attr[]" id="search_selection_attr" multiple title="Select Concentration">
                                        @foreach ($concentrations as $key => $attr_concentration)
                                            <option class="p_right dir_rtl" data-tokens="{{$attr_concentration}}" value="{{$attr_concentration}}">{{$attr_concentration}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if(!empty($sizes))
                                <div class="form-group col the-w-1" id="show_selection_attr_size" style="display: none;">
                                    <select class="selectpicker show-tick" data-selected-text-format="count"
                                            data-max-options="5" data-live-search="true" data-actions-box="true"
                                            name="search_selection_attr[]" id="search_selection_attr" multiple title="Select Size">
                                        @foreach ($sizes as $key => $attr_size)
                                            <option class="p_right dir_rtl" data-tokens="{{$attr_size}}" value="{{$attr_size}}">{{$attr_size}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if(!empty($colors))
                                <div class="form-group col the-w-1" id="show_selection_attr_color" style="display: none;">
                                    <select class="selectpicker show-tick" data-selected-text-format="count"
                                            data-max-options="5" data-live-search="true" data-actions-box="true"
                                            name="search_selection_attr[]" id="search_selection_attr" multiple title="Select Color">
                                        @foreach ($colors as $key => $attr_color)
                                            <option class="p_right dir_rtl" data-tokens="{{$attr_color}}" value="{{$attr_color}}">{{$attr_color}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if(!empty($textures))
                                <div class="form-group col the-w-1" id="show_selection_attr_texture" style="display: none;">
                                    <select class="selectpicker show-tick" data-selected-text-format="count"
                                            data-max-options="5" data-live-search="true" data-actions-box="true"
                                            name="search_selection_attr[]" id="search_selection_attr" multiple title="Select Formulation">
                                        @foreach ($textures as $key => $attr_texture)
                                            <option class="p_right dir_rtl" data-tokens="{{$attr_texture}}" value="{{$attr_texture}}">{{$attr_texture}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if(!empty($skin_types))
                                <div class="form-group col the-w-1" id="show_selection_attr_skin_types" style="display: none;">
                                    <select class="selectpicker show-tick" data-selected-text-format="count"
                                            data-max-options="5" data-live-search="true" data-actions-box="true"
                                            name="search_selection_attr[]" id="search_selection_attr" multiple title="Select Skin Type">
                                        @foreach ($skin_types as $key => $attr_skin_type)
                                            <option class="p_right dir_rtl" data-tokens="{{$attr_skin_type}}" value="{{$attr_skin_type}}">{{$attr_skin_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if(!empty($area_of_applies))
                                <div class="form-group col the-w-1" id="show_selection_attr_area_apply" style="display: none;">
                                    <select class="selectpicker show-tick" data-selected-text-format="count"
                                            data-max-options="5" data-live-search="true" data-actions-box="true"
                                            name="search_selection_attr[]" id="search_selection_attr" multiple title="Select Area of Application">
                                        @foreach ($area_of_applies as $key => $attr_area_apply)
                                            <option class="p_right dir_rtl" data-tokens="{{$attr_area_apply}}" value="{{$attr_area_apply}}">{{$attr_area_apply}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @endif
                            @if(!empty($categories) || !empty($brands) || !empty($attributes) )
                        <div class="form-group col dir_rtl">
                            <input type="button" class="btn btn-success w-45"
                                   style="float: right;" name="clear_search"
                                   id="clear_search" value="Clear">
                            <a href="/get_chart_excel" class="download_report" id="report_excel"><i
                                    class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i></a>
                        </div>
                            @endif
                    </div>


                    <input type="hidden" name="page_number" id="page_number">
                </form>
            </div>
        </div>
        <br>

        <table class="table dataTable no-footer" id="res_search_datatable">

            @include('search_table', ['data' => $data])
        </table>
        <div id="pagination_links">
            <input type="hidden" name="last_page" id="last_page" value="{{ $data->lastPage() }}">
            <?php echo $data->links(); ?>
        </div>

        <!-- Results Search // -->

        <!-- the-all-buttons-1 // -->
    </div>
    <!-- container // -->
</div>
<!-- the-table-content-1 // -->

@include('layouts/footer')
<script src="./asset/js/filters.js?v=4"></script>
</body>

</html>
