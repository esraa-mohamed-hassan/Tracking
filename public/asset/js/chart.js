function dateRang(sku,value){
    $.ajax({
        url: '/chart_filter?sku=' + sku + '&filter=' + value + '',
        type: 'GET',
        success: function(response) {
            console.log(response.time, response.dataset);
            $('#overlay').hide();
            chart.destroy();
            var options = {
                chart: {
                    type: 'line',
                    height: 400,
                    width: '100%',
                    zoom: {
                        enabled: false,
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: false,
                        },
                    },
                },
                plotOptions: {
                    bar: {
                        "horizontal": false
                    }
                },
                colors: ["#ff6384", "#ffc63b", "#007bff", "#80effe"],
                series: response.dataset,
                dataLabels: {
                    enabled: false
                },
                labels: ["Dorepha", "Maesta", "GoldenScent", "NiceOne"],
                title: {
                    text: "Prices"
                },
                subtitle: {
                    text: '',
                    align: ''
                },
                xaxis: {
                    type: 'category',
                    categories: response.time,
                },

                noData: {
                    text: undefined,
                    align: 'center',
                    verticalAlign: 'middle',
                    offsetX: 0,
                    offsetY: 0,
                    style: {
                        color: undefined,
                        fontSize: '14px',
                        fontFamily: undefined
                    }
                },
                stroke: {
                    curve: 'stepline',
                },
                grid: {
                    "show": true
                },
            }

            var chart1 = new ApexCharts(document.querySelector("#draw_chart"),
                options);
            chart1.render();
        }
    });
}

if($('.apexcharts-legend').text() == ''){
    $('input[type="radio"]').removeClass('active');
    $('input[type="radio"]').removeAttr('checked');
    $('.three_months').attr("checked", "checked");
    $('.three_months').addClass("active");
    $('#overlay').show();
    let sku = $('#sku').text();
    let value = 'three_months';
    dateRang(sku,value);
}

$('input[type="radio"]').on('click', (function() {
    if ($(this).is(":checked")) {
        $('input[type="radio"]').removeClass('active');
        $(this).addClass("active")
        let value = $(this).val();
        console.log(value);
        let sku = $('#sku').text();
        $('#report_pdf').attr('href', '/get_chart_pdf?sku=' + sku + '&filter=' + value + '');
        $('#report_excel').attr('href', '/get_chart_excel?sku=' + sku + '&filter=' + value + '');
        $('#overlay').show();
        $.ajax({
            url: '/chart_filter?sku=' + sku + '&filter=' + value + '',
            type: 'GET',
            success: function(response) {
                console.log(response.time, response.dataset);
                $('#overlay').hide();
                chart.destroy();
                var options = {
                    chart: {
                        type: 'line',
                        height: 400,
                        width: '100%',
                        zoom: {
                            enabled: false,
                        },
                        toolbar: {
                            show: true,
                            tools: {
                                download: false,
                            },
                        },
                    },
                    plotOptions: {
                        bar: {
                            "horizontal": false
                        }
                    },
                    colors: ["#ff6384", "#ffc63b", "#007bff", "#80effe"],
                    series: response.dataset,
                    dataLabels: {
                        enabled: false
                    },
                    labels: ["Dorepha", "Maesta", "GoldenScent", "NiceOne"],
                    title: {
                        text: "Prices"
                    },
                    subtitle: {
                        text: '',
                        align: ''
                    },
                    xaxis: {
                        type: 'category',
                        categories: response.time,
                    },

                    noData: {
                        text: undefined,
                        align: 'center',
                        verticalAlign: 'middle',
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                            color: undefined,
                            fontSize: '14px',
                            fontFamily: undefined
                        }
                    },
                    stroke: {
                        curve: 'stepline',
                    },
                    grid: {
                        "show": true
                    },
                }

                var chart1 = new ApexCharts(document.querySelector("#draw_chart"),
                    options);
                chart1.render();
            }
        });
    }
}));

var url = window.location.pathname;
if (url == '/search' || url == '/searching' || url == '/chart') {
    $('.sidenav .dropdown-btn').removeClass('active');
    $('.sidenav a.input_active').addClass('active');
}
