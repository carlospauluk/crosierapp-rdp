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

    /**
     *
     *
     *
     * GRÁFICO DE ESTOQUE
     *
     *
     *
     */


    GoogleCharts.load(drawChart);

    function drawChart() {


        $.getJSON(
            Routing.generate('relEstoque01_graficoTotalEstoquePorFilial'),
            function (results) {

                const data = new google.visualization.DataTable();
                data.addColumn('string', 'Filial');
                data.addColumn('number', 'Total Preço Venda');
                data.addColumn('number', 'Total Custo Médio');

                $.each(results, function (index, value) {
                    let totalVenda = parseFloat(value.total_venda);
                    let totalVendaF = Numeral(totalVenda).format('$ 0.0,[00]');

                    let totalCustoMedio = parseFloat(value.total_custo_medio);
                    let totalCustoMedioF = Numeral(totalCustoMedio).format('$ 0.0,[00]');

                    data.addRow(
                        [
                            value.desc_filial,
                            {'v': totalVenda, 'f': totalVendaF},
                            {'v': totalCustoMedio, 'f': totalCustoMedioF},
                        ]
                    );
                });

                let options = {
                    is3D: true,
                    animation: {
                        startup: true,
                        duration: 1000,
                        easing: 'out',
                    },
                };


                let chart = new google.visualization.ColumnChart(document.getElementById('chart_relEstoque01'));
                chart.draw(data, options);

                google.visualization.events.addListener(chart, 'select', selectHandler);

                function selectHandler() {
                    let selection = chart.getSelection();
                    if (selection && selection[0] && selection[0].row !== 'undefined') {
                        let descFilial = data.getFormattedValue(selection[0].row, 0);
                        if (descFilial) {
                            window.location = Routing.generate('relEstoque01_list',
                                {
                                    'filter':
                                        {
                                            'descFilial': descFilial,
                                        }
                                });
                        }
                    }
                }

            }
        );


    }


});

