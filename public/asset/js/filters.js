/*$(window).on('hashchange', function () {
    if (window.location.hash) {
        var page = window.location.hash.replace('?page=', '');
        // var url =  window.location.href.replace(window.location.hash, '');
        console.log(page);
        if (page == Number.NaN || page <= 0) {
            return false;
        } else {
            getData(page);

        }
    }
});*/

function SelectedAttrs(attr) {
    let res = '';
    if (attr == 'concentration') {
        res = 'concentration';

    } else if (attr == 'size') {
        res = 'size';

    } else if (attr == 'color') {
        res = 'color';

    } else if (attr == 'texture') {
        res = 'texture';

    } else if (attr == 'skin_type') {
        res = 'skin_type';

    } else if (attr == 'area_of_apply') {
        res = 'area_of_apply';
    }

    var selected_attr = jQuery.parseJSON($('#search_selection_attr_hidden').val());
    var all_li = $('#show_selection_attr_'+res+' div.dropdown-menu div.inner ul.dropdown-menu.inner li a span.text');
    var text = '';
    var all_selected = [];
    selected_attr.forEach(function (item, index) {
        // do something with `item`
        all_li.each(function (i, elm) {
            if (elm.textContent === item) {
                $('#show_selection_attr_'+res+' div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-4-' + i + '').parent().addClass('selected');
                $('#show_selection_attr_'+res+' div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-4-' + i + '').addClass('selected');
                $('#show_selection_attr_'+res+' div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-4-' + i + '').attr('aria-selected', 'true');
                var option = $("#search_selection_attr_"+res+".selectpicker option[value='" + elm.textContent + "'").text();
                //We need to show the text inside the span that the plugin show
                $('#search_selection_attr_'+res+'.selectpicker .bootstrap-select .filter-option').text(option);
                all_selected.push(elm.textContent);
                text += elm.textContent + ', ';
                console.log(all_selected);
                $('#show_selection_attr_'+res+' button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(text);
            }
        });
    });
    //Check the selected attribute for the real select
    $('#search_selection_attr.selectpicker').val(selected_attr);
    if (all_selected.length != 0) {
        $('#show_selection_attr_'+res+' button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(all_selected.length + ' items selected');
    }
}

function SelectedCategroies() {
    var selected_attr = jQuery.parseJSON($('#search_categroy_hidden').val());
    var all_li = $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner li a span.text');
    var text = '';
    var all_selected_cat = [];
    selected_attr.forEach(function (item, index) {
        // do something with `item`
        all_li.each(function (i, elm) {
            if (elm.textContent === item) {
                $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-2-' + i + '').parent().addClass('selected');
                $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-2-' + i + '').addClass('selected');
                $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-2-' + i + '').attr('aria-selected', 'true');
                var option = $("#search_categroy.selectpicker option[value='" + elm.textContent + "'").text();
                //We need to show the text inside the span that the plugin show
                $('#search_categroy.selectpicker .bootstrap-select .filter-option').text(option);
                all_selected_cat.push(elm.textContent);
                text += elm.textContent + ', ';
                $('#show_selection_categroy button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(text);
            }
        });
    });
    //Check the selected attribute for the real select
    $('#search_categroy.selectpicker').val(selected_attr);
    if (all_selected_cat.length != 0) {
        $('#show_selection_categroy button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(all_selected_cat.length + ' items selected');
    }
}

function SelectedBrands() {
    var selected_attr = jQuery.parseJSON($('#search_brand_hidden').val());
    var all_li = $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner li a span.text');
    var text = '';
    var all_selected_brand = [];
    selected_attr.forEach(function (item, index) {
        // do something with `item`
        all_li.each(function (i, elm) {
            if (elm.textContent === item) {
                $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-3-' + i + '').parent().addClass('selected');
                $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-3-' + i + '').addClass('selected');
                $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-3-' + i + '').attr('aria-selected', 'true');
                var option = $("#search_brand.selectpicker option[value='" + elm.textContent + "'").text();
                //We need to show the text inside the span that the plugin show
                $('#search_brand.selectpicker .bootstrap-select .filter-option').text(option);
                all_selected_brand.push(elm.textContent);
                text += elm.textContent + ', ';
                $('#show_selection_brand button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(text);
            }
        });
    });
    //Check the selected attribute for the real select
    $('#search_brand.selectpicker').val(selected_attr);
    if (all_selected_brand.length != 0) {
        $('#show_selection_brand button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(all_selected_brand.length + ' items selected');
    }
}

function CategroyShowSelectpicker() {
    $('#search_categroy.selectpicker').selectpicker('toggle');
    SelectedCategroies();
    $('#search_categroy.selectpicker').on('shown.bs.select', function (e, clickedIndex, isSelected, previousValue) {

        SelectedCategroies();
    });
}

function BrandShowSelectpicker() {
    $('#search_brand.selectpicker').selectpicker('toggle');
    SelectedBrands();
    $('#search_brand.selectpicker').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        SelectedBrands();
        console.log(e.target.selectedOptions.length, $(this).val());
    });
}

function AttrsShowSelectpicker() {
    $('#search_selection_attr.selectpicker').selectpicker('toggle');
    SelectedAttrs($('#search_attr_hidden').val());
    $('#search_selection_attr.selectpicker').on('show.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        SelectedAttrs($('#search_attr_hidden').val());
    });
}

function ChangeAttr(attr) {
    let value = attr;

    if (value == 'concentration') {
        $('#show_selection_attr_concentration').show();
        $('#show_selection_attr_size').hide();
        $('#show_selection_attr_color').hide();
        $('#show_selection_attr_texture').hide();
        $('#show_selection_attr_skin_types').hide();
        $('#show_selection_attr_area_apply').hide();
        $('#show_selection_attr_concentration .selectpicker').selectpicker('render');

    } else if (value == 'size') {
        $('#show_selection_attr_concentration').hide();
        $('#show_selection_attr_size').show();
        $('#show_selection_attr_color').hide();
        $('#show_selection_attr_texture').hide();
        $('#show_selection_attr_skin_types').hide();
        $('#show_selection_attr_area_apply').hide();
        $('#show_selection_attr_size .selectpicker').selectpicker('render');


    } else if (value == 'color') {
        $('#show_selection_attr_concentration').hide();
        $('#show_selection_attr_size').hide();
        $('#show_selection_attr_color').show();
        $('#show_selection_attr_texture').hide();
        $('#show_selection_attr_skin_types').hide();
        $('#show_selection_attr_area_apply').hide();
        $('#show_selection_attr_color .selectpicker').selectpicker('render');


    } else if (value == 'texture') {
        $('#show_selection_attr_concentration').hide();
        $('#show_selection_attr_size').hide();
        $('#show_selection_attr_color').hide();
        $('#show_selection_attr_texture').show();
        $('#show_selection_attr_skin_types').hide();
        $('#show_selection_attr_area_apply').hide();
        $('#show_selection_attr_texture .selectpicker').selectpicker('render');


    } else if (value == 'skin_type') {
        $('#show_selection_attr_concentration').hide();
        $('#show_selection_attr_size').hide();
        $('#show_selection_attr_color').hide();
        $('#show_selection_attr_texture').hide();
        $('#show_selection_attr_skin_types').show();
        $('#show_selection_attr_area_apply').hide();
        $('#show_selection_attr_skin_types .selectpicker').selectpicker('render');


    } else if (value == 'area_of_apply') {
        $('#show_selection_attr_concentration').hide();
        $('#show_selection_attr_size').hide();
        $('#show_selection_attr_color').hide();
        $('#show_selection_attr_texture').hide();
        $('#show_selection_attr_skin_types').hide();
        $('#show_selection_attr_area_apply').show();
        $('#show_selection_attr_area_apply .selectpicker').selectpicker('render');

    }
}

function CheckEmpty() {
    if (($('#search_brand.selectpicker').val() == "" || $('#search_brand.selectpicker').val() == undefined) &&
        ($('#search_attr.selectpicker').val() == "" ||  $('#search_attr.selectpicker').val() == undefined) &&
        ($('#search_selection_attr.selectpicker').val() == undefined || $('#search_selection_attr.selectpicker').val() == "") &&
        ($('#search_categroy.selectpicker').val() == "" || $('#search_categroy.selectpicker').val() == undefined)&&
         $('#search_input').val() == ''
    ) {
        console.log(11111);
        $('#search_input').attr('required', 'required');
    } else {
        console.log(22222);
        $('#search_input').removeAttr('required');

    }
}

function getData(page) {
    let search_categroy = $('#search_categroy').val();
    let search_brand = $('#search_brand').val();
    let search_attr = $('#search_attr').val();
    let search_selection_attr = $('#search_selection_attr').val();
    let search = $('#search_input').val();
    $.ajax({
        url: '/data?page=' + page,
        type: 'get',
        data: {
            search_categroy,
            search_brand,
            search_attr,
            search_selection_attr,
            search
        }
    })
        .done(function (response) {
            console.log(response);
            $('#overlay').hide();
            $('#res_search_datatable').html(response.html);
            $('#pagination_links').html(response.html.links);
            $('.pagination li a[href="/searching#?page=' + page + '"]').parent('li').addClass('active');
            $('.pagination li a[href="/searching#?page=' + page + '"]').addClass("active");
            location.hash = '?page=' + page;
            $('#page_number').val(page);
        });
}

$(document).on('click', '#clear_search', function (e) {
    $('.pagination').hide();
    $('#show_selection_attr').hide();
    $('#search_input').val('');
    $('#search_input_hidden').val('');
    $('#search_categroy_hidden').val('[]');
    $('#search_brand_hidden').val('[]');
    $('#search_attr_hidden').val('');
    $('#search_selection_attr_hidden').val('[]');
    $('.selectpicker').selectpicker('val', '');
    CheckEmpty();

    $('#res_search_datatable').html('</tbody>' +
        '<thead class="thead-dark">' +
        '<tr>' +
        '<th scope="col"></th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        '<tr>' +
        ' <td colspan="2" style="text-align: center;">No results</td>' +
        '</tr>' +
        '</tbody>');

});

$(document).ready(function () {

    var url = window.location.pathname;
    if (url == '/search' || url == '/searching' || url == '/chart') {
        $('.sidenav .dropdown-btn').removeClass('active');
        $('.sidenav a.profile_active').removeClass('active');
        $('.sidenav a.input_active').addClass('active');
    }

    $('#show_selection_attr_concentration').hide();
    $('#show_selection_attr_size').hide();
    $('#show_selection_attr_color').hide();
    $('#show_selection_attr_texture').hide();
    $('#show_selection_attr_skin_types').hide();
    $('#show_selection_attr_area_apply').hide();

    $('#search_input_hidden').val($('#search_input').val());

    if ($('#search_categroy_hidden').val() !== '[]') {
        SelectedCategroies();
    }

    if ($('#search_brand_hidden').val() !== '[]') {
        SelectedBrands();
    }

    if ($('#search_attr_hidden').val() !== '') {
        $('#search_attr.selectpicker').selectpicker('val', $('#search_attr_hidden').val());
        ChangeAttr($('#search_attr_hidden').val());
        SelectedAttrs($('#search_attr_hidden').val());
    }

    $('.pagination li[aria-current="page"]').html('<a class="page-link" href="/searching#?page=1">1</a>');


    $(document).on('click', '.pagination a', function (event) {
        event.preventDefault();
        $('#overlay').show();

        $('li').removeClass('active');
        $(this).parent('li').addClass('active');

        var url = $(this).attr('href');
        var page = $(this).attr('href').split('page=')[1];
        getData(page);
        window.history.pushState("", "", url);


        if (page > 1) {
            var prev_page = parseInt(page) - 1;
            var next_page = parseInt(page) + 1;
            $('.pagination li[aria-label="« Previous"]').removeClass('disabled');
            $('.pagination li[aria-label="« Previous"]').html('<a class="page-link" rel="prev" aria-label="« Previous" href="/searching#?page=' + prev_page + '"> ‹ </a>');
            $('.pagination li a[aria-label="Next »"]').attr('href', '/searching#?page=' + next_page)
        }

        if (page == 1) {
            $('.pagination li[aria-label="« Previous"]').addClass('disabled');
            $('.pagination li[aria-label="« Previous"]').parent('li').addClass('disabled');
            $('.pagination li[aria-label="« Previous"]').html('<a class="page-link" rel="prev" aria-label="« Previous" href="javascript:void(0)"> ‹ </a>');
        }

        $('.pagination li a[aria-label="« Previous"]').on('click', function () {
            var $activeLi = $('.pagination').find("li.active").removeClass('active');
            var $activea = $('.pagination').find("a.active").removeClass('active');
            var prev_page = parseInt(page) - 1;
            $('.pagination li a[href="/searching#?page=' + prev_page + '"]').addClass("active");

            $('.pagination li a[aria-label="« Previous"]').attr('href', '/searching#?page=' + prev_page)

        });

        if ($('#last_page').val() == page) {
            $('.pagination li a[aria-label="Next »"]').addClass('disabled');
            $('.pagination li a[aria-label="Next »"]').parent('li').addClass('disabled');
            $('.pagination li a[aria-label="Next »"]').attr('href', "javascript:void(0)");
        } else {
            $('.pagination li a[aria-label="Next »"]').removeClass('disabled');
            $('.pagination li a[aria-label="Next »"]').parent('li').removeClass('disabled');
            $('.pagination li a[aria-label="Next »"]').attr('href', '/searching#?page=' + (parseInt(page) + 1));
        }

        $('.pagination li a[aria-label="Next »"]').on('click', function () {
            var $activeLi = $('.pagination').find("li.active").removeClass('active');
            var $activea = $('.pagination').find("a.active").removeClass('active');

            var next_page = parseInt(page) + 1;

            $('.pagination li a[href="/searching#?page=' + next_page + '"]').addClass("active");

            $('.pagination li a[aria-label="Next »"]').attr('href', '/searching#?page=' + next_page)
        });


    });
});

setTimeout(function () {
    console.log('settime');
    var brand = $('#search_brand_hidden').val();
    var categroy = $('#search_categroy_hidden').val();
    var attr = $('#search_attr_hidden').val();
    var selection_attr = $('#search_selection_attr_hidden').val();

    if (categroy !== '[]' && brand !== '[]' && attr !== '' && selection_attr !== '[]') {
        BrandShowSelectpicker();
        CategroyShowSelectpicker();
        AttrsShowSelectpicker();

    } else if (categroy !== '[]' && brand !== '[]' && attr == '' && selection_attr == '[]') {
        BrandShowSelectpicker();
        CategroyShowSelectpicker();

    } else if (categroy !== '[]' && brand == '[]' && attr == '' && selection_attr == '[]') {
        CategroyShowSelectpicker();
    } else if (categroy !== '[]' && brand == '[]' && attr !== '' && selection_attr !== '[]') {
        CategroyShowSelectpicker();
        AttrsShowSelectpicker();
    } else if (categroy == '[]' && brand !== '[]' && attr !== '' && selection_attr !== '[]') {
        BrandShowSelectpicker();
        AttrsShowSelectpicker();

    } else if (categroy == '[]' && brand !== '[]' && attr == '' && selection_attr == '[]') {
        BrandShowSelectpicker();

    } else if (categroy == '[]' && brand == '[]' && attr !== '' && selection_attr !== '[]') {
        AttrsShowSelectpicker();

    }

    CheckEmpty();
}, 1000);
