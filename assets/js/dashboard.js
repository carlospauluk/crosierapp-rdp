'use strict';

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';

Numeral.locale('pt-br');

import {GoogleCharts} from 'google-charts';

import Moment from 'moment';

Moment.locale('pt-BR');


import 'daterangepicker';

$(document).ready(function () {


    let $filterDts = $('#filterDts').daterangepicker(
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
        drawChart();
    });


    // -- chart


    GoogleCharts.load(drawChart);

    function drawChart() {

        Pace.ignore(function () {

            $.getJSON(
                Routing.generate('relVendas01_totalPorFornecedor') + '/?filterDts=' + $filterDts.val(),
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
                        is3D: false,
                        sliceVisibilityThreshold: .009,
                        animation: {
                            startup: true,
                            duration: 1000,
                            easing: 'out',
                        },
                    };


                    var chart = new google.visualization.PieChart(document.getElementById('chart_totalPorFornecedor'));
                    chart.draw(data, options);

                }
            );


            $.getJSON(
                Routing.generate('relVendas01_totalPorVendedor') + '/?filterDts=' + $filterDts.val(),
                function (results) {

                    const data = new google.visualization.DataTable();
                    data.addColumn('string', 'Vendedor');
                    data.addColumn('number', 'Total');

                    $.each(results, function (index, value) {
                        let v = parseFloat(value.total_venda);
                        let f = Numeral(v).format('$ 0.0,[00]');
                        data.addRow([value.nome_vendedor, {'v': v, 'f': f}]);
                    });

                    var options = {
                        is3D: true,
                        animation: {
                            startup: true,
                            duration: 1000,
                            easing: 'out',
                        },
                    };


                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_totalPorVendedor'));
                    chart.draw(data, options);

                }
            );
        });


    }




});