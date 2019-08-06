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

    if (window.history && window.history.pushState) {

        window.history.pushState('forward', null, './#forward');

        $(window).on('popstate', function() {
            alert('Back button was pressed.');
        });

    }

    /**
     *
     *
     *
     * GRÁFICOS DE VENDAS
     *
     *
     *
     */

    let $btnVendasSearch = $('#btnVendasSearch');
    $btnVendasSearch.on('click', function () {
        drawChart_vendas();
    });

    let $filter_vendas_loja = $('#filter_vendas_loja');
    let $filter_vendas_grupo = $('#filter_vendas_grupo');


    $filter_vendas_loja.select2({
        data: $filter_vendas_loja.data('options')
    });
    if ($filter_vendas_loja.data('val')) {
        $filter_vendas_loja.val($filter_vendas_loja.data('val')).trigger('change');
    }
    $filter_vendas_loja.on('select2:select', function () {
        drawChart_vendas();
    });


    $filter_vendas_grupo.select2({
        data: $filter_vendas_grupo.data('options')
    });
    if ($filter_vendas_grupo.data('val')) {
        $filter_vendas_grupo.val($filter_vendas_grupo.data('val')).trigger('change');
    }
    $filter_vendas_grupo.on('select2:select', function () {
        drawChart_vendas();
    });


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
    }).on('blur', function (ev, picker) {
        drawChart_vendas();
    });


    GoogleCharts.load(drawChart_vendas);

    function drawChart_vendas() {

        Pace.ignore(function () {

            $.getJSON(
                Routing.generate('relVendas01_totalPorFornecedor') + '/?filterDts=' + $filter_vendas_dts.val() + '&loja=' + $filter_vendas_loja.val() + '&grupo=' + $filter_vendas_grupo.val(),
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


                    chart_totalPorFornecedor = new google.visualization.PieChart(document.getElementById('chart_totalPorFornecedor'));
                    chart_totalPorFornecedor.draw(data, options);

                    google.visualization.events.addListener(chart_totalPorFornecedor, 'select', selectHandler);

                    function selectHandler() {
                        let selection = chart_totalPorFornecedor.getSelection();
                        let nomeFornec = data.getFormattedValue(selection[0].row, 0);
                        if (nomeFornec) {
                            window.location = Routing.generate('relVendas01_itensVendidosPorFornecedor',
                                {
                                    'filter':
                                        {
                                            'dts': $filter_vendas_dts.val(),
                                            'nomeFornec': nomeFornec,
                                            'grupo': $filter_vendas_grupo.val(),
                                            'loja': $filter_vendas_loja.val()
                                        }
                                });
                        }
                    }

                }
            );


            $.getJSON(
                Routing.generate('relVendas01_totalPorVendedor') + '/?filterDts=' + $filter_vendas_dts.val() + '&loja=' + $filter_vendas_loja.val() + '&grupo=' + $filter_vendas_grupo.val(),
                function (results) {
                    let rentabilidadeGeral = Numeral(parseFloat(results.rentabilidadeGeral)).format('0,0.00');

                    $('#chart_totalPorVendedor_rentabilidade').html(' ' + rentabilidadeGeral);

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
                            'Total vendido: <b>' + totalVendaF + '</b><br />Rentabilidade: ' + rentF + '&#37;</div>';

                        data.addRow([
                            value.nome_vendedor,
                            {'v': totalVenda, 'f': totalVendaF},
                            t,
                            {'v': rent, 'f': rentF},
                            t]);
                    });

                    var options = {
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


                    chart_totalPorVendedor = new google.visualization.ColumnChart(document.getElementById('chart_totalPorVendedor'));
                    chart_totalPorVendedor.draw(data, options);

                    google.visualization.events.addListener(chart_totalPorVendedor, 'select', selectHandler);

                    function selectHandler() {
                        let selection = chart_totalPorVendedor.getSelection();
                        let vendedor = data.getFormattedValue(selection[0].row, 0);
                        if (vendedor) {
                            window.location = Routing.generate('relVendas01_listPreVendasPorVendedor',
                                {
                                    filter: {
                                        'dts': $filter_vendas_dts.val(),
                                        'vendedor': vendedor,
                                        'grupo': $filter_vendas_grupo.val(),
                                        'loja': $filter_vendas_loja.val()
                                    }
                                });
                        }
                    }

                }
            );

        });
    }


    /**
     *
     *
     *
     * GRÁFICO DE CONTAS A PAGAR/RECEBER
     *
     *
     *
     */

    let $btnContasPagRecSearch = $('#btnContasPagRecSearch');
    $btnContasPagRecSearch.on('click', function () {
        drawChart_contasPagRec();
    });


    let $filter_contasPagRec_filial = $('#filter_contasPagRec_filial');

    $filter_contasPagRec_filial.select2({
        data: $filter_contasPagRec_filial.data('options')
    });
    if ($filter_contasPagRec_filial.data('val')) {
        $filter_contasPagRec_filial.val($filter_contasPagRec_filial.data('val')).trigger('change');
    }
    $filter_contasPagRec_filial.on('select2:select', function () {
        // handleLocalizadorPorFilial();
        drawChart_contasPagRec();
    });


    let $filter_contasPagRec_localizador = $('#filter_contasPagRec_localizador');

    $filter_contasPagRec_localizador.select2({
        data: $filter_contasPagRec_localizador.data('options')
    });
    if ($filter_contasPagRec_localizador.data('val')) {
        $filter_contasPagRec_localizador.val($filter_contasPagRec_localizador.data('val')).trigger('change');
    }
    $filter_contasPagRec_localizador.on('select2:select', function () {
        drawChart_contasPagRec();
    });


    /**
     * RTA...
     *
     * Se a filial selecionada for de TELÊMACO BORBA, só pode selecionar o localizador 91.
     * Se a filial selecionada for a ACESSÓRIOS, só pode selecionar o localizador 92
     */
        // function handleLocalizadorPorFilial() {
        //     $filter_contasPagRec_localizador.find('option').each(function (i, e) {
        //         $(e).removeAttr('disabled');
        //     });
        //
        //     if ($filter_contasPagRec_filial.val()) {
        //         if ($filter_contasPagRec_filial.val().startsWith('96')) {
        //
        //             $filter_contasPagRec_localizador.find('option').each(function (i, e) {
        //                 $(e).attr('disabled', 'true')
        //             });
        //             $filter_contasPagRec_localizador.find('option[value^="91"]').removeAttr('disabled');
        //             $filter_contasPagRec_localizador.find('option[value^="91"]').attr('selected', 'true');
        //             $filter_contasPagRec_localizador.trigger('change');
        //         } else if ($filter_contasPagRec_filial.val().startsWith('94')) {
        //
        //             $filter_contasPagRec_localizador.find('option').each(function (i, e) {
        //                 $(e).attr('disabled', 'true')
        //             });
        //             $filter_contasPagRec_localizador.find('option[value^="92"]').removeAttr('disabled');
        //             $filter_contasPagRec_localizador.find('option[value^="92"]').attr('selected', 'true');
        //             $filter_contasPagRec_localizador.trigger('change');
        //         }
        //     }
        //
        //     $filter_contasPagRec_localizador.select2();
        // }


        // handleLocalizadorPorFilial();


        // filtro por período
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


    GoogleCharts.load(drawChart_contasPagRec);

    function drawChart_contasPagRec() {

        Pace.ignore(function () {


            $.getJSON(
                Routing.generate('relCtsPagRec01_rel01') + '/?filterDts=' + $filter_contasPagRec_dts.val() + '&filial=' + $filter_contasPagRec_filial.val() + '&localizador=' + $filter_contasPagRec_localizador.val(),
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
                                {'v': aPagarV, 'f': aPagarF},
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
                            window.location = Routing.generate('relCtsPagRec01_list', {
                                filter: {
                                    'dts': dt + ' - ' + dt,
                                    'filial': $filter_contasPagRec_filial.val(),
                                    'localizador': $filter_contasPagRec_localizador.val()
                                }
                            });
                        }
                    }
                }
            );
        });
    }


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
                Routing.generate('relCompFor01_totalPorFornecedor') + '/?filterDts=' + $filter_relCompFor01_dts.val(),
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
                        // relCompFor01_itensCompradosPorFornecedor
                        let selection = chart_relCompFor01.getSelection();
                        let nomeFornec = data.getFormattedValue(selection[0].row, 0);
                        if (nomeFornec) {
                            window.location = Routing.generate('relCompFor01_itensCompradosPorFornecedor',
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

