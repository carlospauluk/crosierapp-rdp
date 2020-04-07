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
import $ from "jquery";



$(document).ready(function () {

    /**
     *
     *
     *
     * GRÁFICO DE COMPRAS
     *
     *
     *
     */

    let $btnCompForSearch = $('#btnCompForSearch');
    $btnCompForSearch.on('click', function () {
        drawChart_relCompFor01();
    });

    let $filter_relCompFor01_dts = $('#filter_relCompFor01_dts').daterangepicker(
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
        drawChart_relCompFor01();
    });


    GoogleCharts.load(drawChart_relCompFor01);

    function drawChart_relCompFor01() {

        Pace.ignore(function () {

            $.getJSON(
                Routing.generate('relCompFor01_graficoTotalPorFornecedor') + '?filterDts=' + $filter_relCompFor01_dts.val(),
                function (results) {

                    const data = new google.visualization.DataTable();
                    data.addColumn('string', 'Fornecedor');
                    data.addColumn('number', 'Total');

                    $.each(results, function (index, value) {
                        let v = parseFloat(value.total_compras);
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


                    let chart_relCompFor01 = new google.visualization.PieChart(document.getElementById('chart_relCompFor01'));
                    chart_relCompFor01.draw(data, options);

                    google.visualization.events.addListener(chart_relCompFor01, 'select', selectHandler);

                    function selectHandler() {
                        let selection = chart_relCompFor01.getSelection();
                        let nomeFornec = data.getFormattedValue(selection[0].row, 0);
                        if (nomeFornec) {
                            window.location = Routing.generate('relCompFor01_listItensCompradosPorFornecedor',
                                {
                                    'filter':
                                        {
                                            'dts': $filter_relCompFor01_dts.val(),
                                            'nomeFornec': nomeFornec
                                        }
                                });
                        }
                    }

                }
            );


        });
    }





});

