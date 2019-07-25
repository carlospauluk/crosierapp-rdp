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

    let chart_totalPorFornecedor;
    let chart_totalPorVendedor;

    let $filter_vendas_dts = $('#filter_vendas_dts').daterangepicker(
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
        drawChart_vendas();
    });


    let $filter_contasPagRec_dts = $('#filter_contasPagRec_dts').daterangepicker(
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
                'Próximo mês': [Moment().add(1, 'month').startOf('month'), Moment().add(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true
        },
        function (start, end, label) {

        }
    ).on('apply.daterangepicker', function (ev, picker) {
        drawChart_contasPagRec();
    });


    // -- chart


    GoogleCharts.load(drawChart_vendas);

    function drawChart_vendas() {

        Pace.ignore(function () {

            $.getJSON(
                Routing.generate('relVendas01_totalPorFornecedor') + '/?filterDts=' + $filter_vendas_dts.val(),
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
                        sliceVisibilityThreshold: .014,
                        animation: {
                            startup: true,
                            duration: 1000,
                            easing: 'out',
                        },
                    };


                    chart_totalPorFornecedor = new google.visualization.PieChart(document.getElementById('chart_totalPorFornecedor'));
                    chart_totalPorFornecedor.draw(data, options);

                    google.visualization.events.addListener(chart_totalPorFornecedor, 'select', selectHandler);

// The selection handler.
// Loop through all items in the selection and concatenate
// a single message from all of them.
                    function selectHandler() {
                        var selection = chart_totalPorFornecedor.getSelection();
                        var message = '';
                        for (var i = 0; i < selection.length; i++) {
                            var item = selection[i];
                            if (item.row != null && item.column != null) {
                                var str = data.getFormattedValue(item.row, item.column);
                                message += '.{row:' + item.row + ',column:' + item.column + '} = ' + str + '\n';
                            } else if (item.row != null) {
                                var str = data.getFormattedValue(item.row, 0);
                                message += '..{row:' + item.row + ', column:none}; value (col 0) = ' + str + '\n';
                            } else if (item.column != null) {
                                var str = data.getFormattedValue(0, item.column);
                                message += '...{row:none, column:' + item.column + '}; value (row 0) = ' + str + '\n';
                            }
                        }
                        if (message == '') {
                            message = 'nothing';
                        }
                        console.log('You selected ' + message);
                    }

                }
            );


            $.getJSON(
                Routing.generate('relVendas01_totalPorVendedor') + '/?filterDts=' + $filter_vendas_dts.val(),
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


                    chart_totalPorVendedor = new google.visualization.ColumnChart(document.getElementById('chart_totalPorVendedor'));
                    chart_totalPorVendedor.draw(data, options);

                }
            );

        });
    }





    GoogleCharts.load(drawChart_contasPagRec);

    function drawChart_contasPagRec() {

        Pace.ignore(function () {


            $.getJSON(
                Routing.generate('relCtsPagRec01_rel01') + '/?filterDts=' + $filter_contasPagRec_dts.val(),
                function (results) {

                    const data = new google.visualization.DataTable();
                    data.addColumn('string', 'Data');
                    data.addColumn('number', 'A Receber');
                    data.addColumn('number', 'A Pagar');

                    $.each(results, function (index, value) {
                        let aPagarV = parseFloat(value.aPagar);
                        let aPagarF = Numeral(aPagarV).format('$ 0.0,[00]');
                        let aReceberV = parseFloat(value.aReceber);
                        let aReceberF = Numeral(aReceberV).format('$ 0.0,[00]');
                        let dtVencto = value.dt_vencto;

                        data.addRow(
                            [
                                value.dtVencto,
                                {'v': aReceberV, 'f': aReceberF},
                                {'v': aPagarV , 'f': aPagarF},
                                ]);

                    });


                    var options = {
                        is3D: true,
                        animation: {
                            startup: true,
                            duration: 1000,
                            easing: 'out',
                        },
                    };


                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_contasPagarReceber'));
                    chart.draw(data, options);

                    google.visualization.events.addListener(chart, 'select', selectHandler);

                    function selectHandler() {
                        let selection = chart.getSelection();
                        let dt = data.getFormattedValue(selection[0].row, 0);
                        if (dt) {
                            window.location = Routing.generate('relCtsPagRec01_list', {filter: {dts: dt + ' - ' + dt}});
                        }
                        

                    }


                }
            );
        });


    }



});