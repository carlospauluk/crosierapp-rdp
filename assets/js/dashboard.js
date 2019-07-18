'use strict';

import routes from '../static/fos_js_routes.json';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
Routing.setRoutingData(routes);

import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
Numeral.locale('pt-br');

import {GoogleCharts} from 'google-charts';

//Load the 'corecharts'. You do not need to provide that as a type.
GoogleCharts.load(drawChart);

function drawChart() {

    $.getJSON(
        Routing.generate('relVendas01_totalPorFornecedor'),
        function (results) {

            const data = new google.visualization.DataTable();
            data.addColumn('string', 'Fornecedor');
            data.addColumn('number', 'Total');

            $.each(results, function( index, value ) {
                let v = parseFloat(value.total_venda);
                let f = Numeral(v).format('$ 0.0,[00]');
                console.log(f);
                data.addRow([value.nome_fornec, {'v':v, 'f':f}]);
            });

            var options = {
                is3D: true,
                sliceVisibilityThreshold: .009
            };


            var chart = new google.visualization.PieChart(document.getElementById('chart_totalPorFornecedor'));
            chart.draw(data, options);

        }
    );


    $.getJSON(
        Routing.generate('relVendas01_totalPorVendedor'),
        function (results) {

            const data = new google.visualization.DataTable();
            data.addColumn('string', 'Vendedor');
            data.addColumn('number', 'Total');

            $.each(results, function( index, value ) {
                let v = parseFloat(value.total_venda);
                let f = Numeral(v).format('$ 0.0,[00]');
                console.log(f);
                data.addRow([value.nome_vendedor, {'v':v, 'f':f}]);
            });

            var options = {
                is3D: true
            };


            var chart = new google.visualization.PieChart(document.getElementById('chart_totalPorVendedor'));
            chart.draw(data, options);

        }
    );


}
