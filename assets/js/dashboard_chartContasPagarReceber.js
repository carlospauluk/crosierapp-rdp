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
                Routing.generate('relCtsPagRec01_graficoCtsPagRec01') + '?filterDts=' + $filter_contasPagRec_dts.val() + '&filial=' + $filter_contasPagRec_filial.val() + '&localizador=' + $filter_contasPagRec_localizador.val(),
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
                        colors: ['lightgreen', 'red']
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

});

