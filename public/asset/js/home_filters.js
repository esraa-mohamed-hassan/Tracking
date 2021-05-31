function SelectedAttrs() {
    var selected_attr = jQuery.parseJSON($('#search_selection_attr_hidden').val());
    var all_li = $('#show_selection_attr div.dropdown-menu div.inner ul.dropdown-menu.inner li a span.text');
    var text = '';
    var all_selected = [];
    selected_attr.forEach(function(item, index) {
        // do something with `item`
        all_li.each(function(i, elm) {
            if (elm.textContent === item) {
                $('#show_selection_attr div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-4-' + i + '').parent().addClass('selected');
                $('#show_selection_attr div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-4-' + i + '').addClass('selected');
                $('#show_selection_attr div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-4-' + i + '').attr('aria-selected', 'true');
                var option = $("#search_selection_attr.selectpicker option[value='" + elm.textContent + "'").text();
                //We need to show the text inside the span that the plugin show
                $('#search_selection_attr.selectpicker .bootstrap-select .filter-option').text(option);
                all_selected.push(elm.textContent);
                text += elm.textContent + ', ';
                $('#show_selection_attr button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(text);
            }
        });
    });
    //Check the selected attribute for the real select
    $('#search_selection_attr.selectpicker').val(selected_attr);
}

function SelectedCategroies() {
    var selected_attr = jQuery.parseJSON($('#search_categroy_hidden').val());
    var all_li = $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner li a span.text');
    var text = '';
    var all_selected = [];
    selected_attr.forEach(function(item, index) {
        // do something with `item`
        all_li.each(function(i, elm) {
            if (elm.textContent === item) {
                $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-2-' + i + '').parent().addClass('selected');
                $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-2-' + i + '').addClass('selected');
                $('#show_selection_categroy div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-2-' + i + '').attr('aria-selected', 'true');
                var option = $("#search_categroy.selectpicker option[value='" + elm.textContent + "'").text();
                //We need to show the text inside the span that the plugin show
                $('#search_categroy.selectpicker .bootstrap-select .filter-option').text(option);
                all_selected.push(elm.textContent);
                text += elm.textContent + ', ';
                $('#show_selection_categroy button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(text);
            }
        });
    });
    //Check the selected attribute for the real select
    $('#search_categroy.selectpicker').val(selected_attr);

}

function SelectedBrands() {
    var selected_attr = jQuery.parseJSON($('#search_brand_hidden').val());
    var all_li = $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner li a span.text');
    var text = '';
    var all_selected = [];
    selected_attr.forEach(function(item, index) {
        // do something with `item`
        all_li.each(function(i, elm) {
            if (elm.textContent === item) {
                $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-3-' + i + '').parent().addClass('selected');
                $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-3-' + i + '').addClass('selected');
                $('#show_selection_brand div.dropdown-menu div.inner ul.dropdown-menu.inner a#bs-select-3-' + i + '').attr('aria-selected', 'true');
                var option = $("#search_brand.selectpicker option[value='" + elm.textContent + "'").text();
                //We need to show the text inside the span that the plugin show
                $('#search_brand.selectpicker .bootstrap-select .filter-option').text(option);
                all_selected.push(elm.textContent);
                text += elm.textContent + ', ';
                $('#show_selection_brand button.btn.dropdown-toggle.btn-light div.filter-option div.filter-option-inner-inner').html(text);
            }
        });
    });
    //Check the selected attribute for the real select
    $('#search_brand.selectpicker').val(selected_attr);
}

function CategroyShowSelectpicker() {
    $('#search_categroy.selectpicker').selectpicker('toggle');
    SelectedCategroies();
    $('#search_categroy.selectpicker').on('shown.bs.select', function(e, clickedIndex, isSelected, previousValue) {

        SelectedCategroies();
    });
}

function BrandShowSelectpicker() {
    $('#search_brand.selectpicker').selectpicker('toggle');
    SelectedBrands();
    $('#search_brand.selectpicker').on('show.bs.select', function(e, clickedIndex, isSelected, previousValue) {
        SelectedBrands();
        console.log(e.target.selectedOptions.length, $(this).val());
    });
}

function AttrsShowSelectpicker() {
    $('#search_selection_attr.selectpicker').selectpicker('toggle');
    SelectedAttrs();
    $('#search_selection_attr.selectpicker').on('show.bs.select', function(e, clickedIndex, isSelected, previousValue) {
        SelectedAttrs();
    });
}

function ajaxAttr(title, value, response) {
    $('#show_selection_attr').show();
    $('#show_selection_attr').html(
        '<select class="selectpicker show-tick" data-selected-text-format="count" data-max-options="5" data-live-search="true" data-actions-box="true"  name="search_selection_attr[]" id="search_selection_attr" multiple title="' +
        `${title}` + '">' +
        '</select>');
    $.each(response, function(k, v) {

        if (value == 'concentration') {
            res = v.concentration;

        } else if (value == 'size') {
            res = v.size;

        } else if (value == 'color') {
            res = v.color;

        } else if (value == 'texture') {
            res = v.texture;

        } else if (value == 'skin_type') {
            res = v.skin_type;

        } else if (value == 'area_of_apply') {
            res = v.area_of_apply;
        }
        // console.log(res);
        $(`<option class="p_right dir_rtl" data-tokens="${res}" value="${res}">${res}</option> `)
            .val(res).text(res).appendTo('#search_selection_attr');
    });
    $('#show_selection_attr .selectpicker').selectpicker('render');
}

function ChangeAttr(attr) {
    let value = attr;
    let url = '';
    let res = '';
    let title = '';

    if (value == 'concentration') {
        url = '/get_concentration';
        title = `Select Concentration`;

    } else if (value == 'size') {
        url = '/get_size';
        title = `Select Size`;
    } else if (value == 'color') {
        url = '/get_color';
        title = `Select Color`;

    } else if (value == 'texture') {
        url = '/get_texture';
        title = `Select Formulation`;

    } else if (value == 'skin_type') {
        url = '/get_skin_type';
        title = `Select Skin Type`;

    } else if (value == 'area_of_apply') {
        url = '/get_area_of_apply';
        title = `Select Area of Application`;
    }

    $('#show_selection_attr').hide();
    $('#show_selection_attr').html('');
    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            if (response.status == 'success') {
                ajaxAttr(title, value, response.data);
            } else{
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.status == 'success') {
                            ajaxAttr(title, value, response.data);
                        }
                    }
                });
            }
        }
    });
}

function CheckEmpty() {
    if ($('#search_brand.selectpicker').val() == "" && $('#search_attr.selectpicker').val() == "" && $('#search_selection_attr.selectpicker').val() == undefined && $('#search_categroy.selectpicker').val() == "" && $('#search_input').val() == '') {
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
        .done(function(response) {
            $('#overlay').hide();
            $('#res_search_datatable').html(response.html);
            $('#pagination_links').html(response.html.links);
            $('.pagination li a[href="/searching#?page=' + page + '"]').parent('li').addClass('active');
            $('.pagination li a[href="/searching#?page=' + page + '"]').addClass("active");
            location.hash = '?page=' + page;
            $('#page_number').val(page);
        });
}

$(document).on('click', '#clear_search', function(e) {
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

$(document).ready(function() {

    if (window.location.hash) {
        window.location.href = window.location.href.replace(window.location.hash, '');
    }

    var url = window.location.pathname;
    if (url == '/search' || url == '/searching' || url == '/chart') {
        $('.sidenav .dropdown-btn').removeClass('active');
        $('.sidenav a.input_active').addClass('active');
        $('.sidenav a.profile_active').removeClass('active');

    }

    $('#show_selection_attr').hide();

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
        SelectedAttrs();
    }
});

setTimeout(function() {
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
    $('#search_brand.selectpicker').click();
    $('#search_categroy.selectpicker').click();
    $('#search_attr.selectpicker').click();
    $('#search_selection_attr.selectpicker').click();
    CheckEmpty();

}, 1000);

