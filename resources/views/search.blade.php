<!doctype html>
<html lang="en">

<head>
    <title>Search</title>
    @include('layouts/header')
    <style>
        .the-w-1 .dropdown {
            width: 100% !important;
        }

        .the-w-1 .dropdown .dropdown-menu.show  {
            display: flex !important;
            flex-direction: column;
        }
        .the-w-1 .dropdown .dropdown-menu ul li {
            order: 1;
        }
        .the-w-1 .dropdown .dropdown-menu ul li.selected {
            order: 0;
        }
        .the-w-1 .dropdown-menu .bs-actionsbox button.bs-select-all{
            display: none!important;
        }

        .the-w-1 .dropdown-menu .bs-actionsbox button.bs-deselect-all{
            width: 100% !important;
            margin-top: 1%;
        }
    </style>
</head>

<body class="open-menu-1">

@include('layouts/menu')

<!-- the-table-content-1 -->
<div class="the-table-content-1 my-0 d-block overflow-auto pb-5 mb-5">
    <!-- container -->
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-9 mt-3">
                <div id="logo" class="text-center">
                    <img src="./asset/images/MaskGroup.png" alt="logo">
                </div>
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
                                    Search
                                </button>
                            </span>
                    </div>

                    <!-- Results Search -->
                    <div class="h4 mt-4 mb-1">
                        <input name="search_categroy_link" type="hidden" value="" id="search_categroy_link_hidden">

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
                            <div class="flex-fill col-auto the-w-1 pb-3" id="show_selection_categroy">
                                <select class="selectpicker show-tick" data-selected-text-format="count" data-actions-box="true" data-live-search="true"
                                        name="search_categroy[]" onchange="CheckEmpty()" id="search_categroy" multiple data-max-options="5"
                                        title="Select Categroy">
                                    @foreach ($categories as $category)
                                        <option class="p_right dir_rtl" data-tokens="{{ $category }}"
                                                value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-fill col-auto the-w-1 pb-3" id="show_selection_brand">
                                <select class="selectpicker show-tick" data-selected-text-format="count" data-actions-box="true" data-live-search="true" name="search_brand[]"
                                        data-max-options="5"
                                        onchange="CheckEmpty()" id="search_brand" multiple title="Select Brand">
                                    @foreach ($brands as $brand)
                                        <option class="p_right dir_rtl" data-tokens="{{ $brand }}"
                                                value="{{ $brand }}">{{ $brand }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-fill col-auto the-w-1 pb-3">
                                <select class="selectpicker show-tick" data-actions-box="true" data-live-search="true" name="search_attr"
                                        onchange="ChangeAttr(this.value),CheckEmpty()" id="search_attr"
                                        title="Select Attributes">
                                    <option class="p_right dir_rtl" data-tokens="Concentration"
                                            value="concentration">
                                        Concentration
                                    </option>
                                    <option class="p_right dir_rtl" data-tokens="Size" value="size">Size</option>
                                    <option class="p_right dir_rtl" data-tokens="Color" value="color">Color</option>
                                    <option class="p_right dir_rtl" data-tokens="Formulation" value="texture">
                                        Formulation
                                    </option>
                                    <option class="p_right dir_rtl" data-tokens="Skin Type" value="skin_type">Skin
                                        Type
                                    </option>
                                    <option class="p_right dir_rtl" data-tokens="Area of Application"
                                            value="area_of_apply">
                                        Area of Application
                                    </option>
                                </select>
                            </div>

                            <div class="flex-fill col-auto the-w-1 pb-3" style="display: none;" id="show_selection_attr"></div>

                            <div class="flex-fill col-auto dir_rtl">
                                <input type="button" class="btn btn-success w-100" style="float: right;"
                                       name="clear_search" id="clear_search" value="Clear">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts/footer')
<script src="./asset/js/home_filters.js?v=2"></script>
</body>
</html>
