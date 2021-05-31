<script>
    var options =
        {
            chart: {
                type: '{!! $chart->type() !!}',
                height: {!! $chart->height() !!},
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
                bar: {!! $chart->horizontal() !!}
            },
            colors: {!! $chart->colors() !!},
            series:
            {!! $chart->dataset() !!},
            dataLabels: {
                enabled: false
            },
            labels: [{!! $chart->labels() !!}],
            title: {
                text: "{!! $chart->title() !!}"
            },
            subtitle: {
                text: '{!! $chart->subtitle() !!}',
                align: '{!! $chart->subtitlePosition() !!}'
            },
            xaxis: {
                type: 'category',
                categories: {!! $chart->xAxis() !!},
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
            grid: {!! $chart->grid() !!},
        }

    var chart = new ApexCharts(document.querySelector("#draw_chart"), options);
    chart.render();

</script>
