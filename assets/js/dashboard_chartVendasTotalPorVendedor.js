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

    let $btnFiltrar = $('#btnFiltrar_chartVendasTotalPorVendedor');
    let $filter_lojas = $('#filter_chartVendasTotalPorVendedor_lojas');
    let $filter_grupos = $('#filter_chartVendasTotalPorVendedor_grupos');


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
            Routing.generate('relVendas01_graficoTotalPorVendedor') + '/?filterDts=' + $filter_dts.val() + '&lojas=' + $filter_lojas.val() + '&grupos=' + $filter_grupos.val(),
            function (results) {
                let rentabilidadeGeral = Numeral(parseFloat(results.rentabilidadeGeral)).format('0,0.00');

                $('#chart_totalPorVendedor_rentabilidade').html('&nbsp;' + rentabilidadeGeral + '&percnt;');

                const data = new google.visualization.DataTable();
                data.addColumn('string', 'Vendedor');
                data.addColumn('number', 'Total');
                data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
                data.addColumn('number', 'Rentabilidade');
                data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});

                $.each(results.dados, function (index, value) {
                    let totalVenda = parseFloat(value.total_venda);
                    let totalVendaF = Numeral(totalVenda).format('$ 0,0.00');

                    let rent = parseFloat(value.rent);
                    let rentF = Numeral(parseFloat(value.rent)).format('0,0.00');

                    let t = '<div style="white-space: nowrap">' + value.nome_vendedor + '<br />' +
                        'Total vendido: <b>' + totalVendaF + '</b><br />Rentabilidade: <b>' + rentF + '%</b></div>';

                    data.addRow([
                        value.nome_vendedor,
                        {'v': totalVenda, 'f': totalVendaF},
                        t,
                        {'v': rent, 'f': rentF},
                        t]);
                });

                let options = {
                    is3D: true,
                    animation: {
                        startup: true,
                        duration: 1000,
                        easing: 'out',
                    },
                    tooltip: {isHtml: true},
                    series: {
                        0: {targetAxisIndex: 0},
                        1: {targetAxisIndex: 1}
                    },
                    vAxes: {
                        // Adds titles to each axis.
                        0: {title: 'Total vendido'},
                        1: {title: 'Rentabilidade'}
                    }

                };


                chart = new google.visualization.ColumnChart(document.getElementById('chart_vendasTotalPorVendedor'));
                chart.draw(data, options);

                google.visualization.events.addListener(chart, 'select', selectHandler);

                function selectHandler() {
                    let selection = chart.getSelection();
                    let vendedor = data.getFormattedValue(selection[0].row, 0);
                    if (vendedor) {
                        window.location = Routing.generate('relVendas01_listPreVendasPorVendedor',
                            {
                                filter: {
                                    'dts': $filter_dts.val(),
                                    'vendedor': vendedor,
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

