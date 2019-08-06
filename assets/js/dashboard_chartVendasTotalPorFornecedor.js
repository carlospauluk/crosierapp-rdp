'use strict';

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
import {GoogleCharts} from 'google-charts';

import Moment from 'moment';
import 'daterangepicker';
import $ from "jquery";

Routing.setRoutingData(routes);

Numeral.locale('pt-br');

Moment.locale('pt-BR');


$(document).ready(function () {

    let $btnFiltrar = $('#btnFiltrar_chartVendasTotalPorFornecedor');
    let $filter_lojas = $('#filter_chartVendasTotalPorFornecedor_lojas');
    let $filter_grupos = $('#filter_chartVendasTotalPorFornecedor_grupos');


    $btnFiltrar.on('click', function () {
        drawChart();
    });

    $filter_lojas.select2({
        tags: true,
        data: $filter_lojas.data('options'),
        allowClear: true,
        tokenSeparators: [','],
    });
    if ($filter_lojas.data('val')) {
        $filter_lojas.val($filter_lojas.data('val').split(',')).trigger('change');
    }
    $filter_lojas.on('select2:select', function () {
        // drawChart();
    });


    $filter_grupos.select2({
        data: $filter_grupos.data('options')
    });
    if ($filter_grupos.data('val')) {
        $filter_grupos.val($filter_grupos.data('val').split(',')).trigger('change');
    }
    $filter_grupos.on('select2:select', function () {
        // drawChart();
    });


    let chart;

    let $filter_dts = $('#filter_chartVendasTotalPorVendedor_dts').daterangepicker(
        {
            opens: 'left',
            autoApply: true,
            locale: {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "De",
                "toLabel": "Até",
                "customRangeLabel": "Custom",
                "daysOfWeek": [
                    "Dom",
                    "Seg",
                    "Ter",
                    "Qua",
                    "Qui",
                    "Sex",
                    "Sáb"
                ],
                "monthNames": [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro"
                ],
                "firstDay": 0
            },
            ranges: {
                'Hoje': [Moment(), Moment()],
                'Ontem': [Moment().subtract(1, 'days'), Moment().subtract(1, 'days')],
                'Últimos 7 dias': [Moment().subtract(6, 'days'), Moment()],
                'Últimos 30 dias': [Moment().subtract(29, 'days'), Moment()],
                'Este mês': [Moment().startOf('month'), Moment().endOf('month')],
                'Mês passado': [Moment().subtract(1, 'month').startOf('month'), Moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true
        },
        function (start, end, label) {

        }
    ).on('apply.daterangepicker', function (ev, picker) {
        // drawChart();
    }).on('blur', function (ev, picker) {
        // 1drawChart();
    });


    GoogleCharts.load(drawChart);

    function drawChart() {

        // Pace.ignore(function () {

        $.getJSON(
            Routing.generate('relVendas01_graficoTotalPorFornecedor') + '/?filterDts=' + $filter_dts.val() + '&lojas=' + $filter_lojas.val() + '&grupos=' + $filter_grupos.val(),
            function (results) {

                const data = new google.visualization.DataTable();
                data.addColumn('string', 'Fornecedor');
                data.addColumn('number', 'Total');

                $.each(results, function (index, value) {
                    let v = parseFloat(value.total_venda);
                    let f = Numeral(v).format('$ 0.0,[00]');
                    data.addRow([value.nome_fornec, {'v': v, 'f': f}]);
                });

                var options = {
                    is3D: true,
                    sliceVisibilityThreshold: .014,
                    animation: {
                        startup: true,
                        duration: 2000,
                        easing: 'out',
                    },
                };


                chart = new google.visualization.PieChart(document.getElementById('chart_vendasTotalPorFornecedor'));
                chart.draw(data, options);

                google.visualization.events.addListener(chart, 'select', selectHandler);

                function selectHandler() {
                    let selection = chart.getSelection();
                    let nomeFornec = data.getFormattedValue(selection[0].row, 0);
                    if (nomeFornec) {
                        window.location = Routing.generate('relVendas01_listItensVendidosPorFornecedor',
                            {
                                'filter':
                                    {
                                        'dts': $filter_dts.val(),
                                        'nomeFornec': nomeFornec,
                                        'grupos': $filter_grupos.val(),
                                        'lojas': $filter_lojas.val()
                                    }
                            });
                    }
                }

            }
        );


        // });
    }

});

