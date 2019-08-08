'use strict';

import Moment from 'moment';


import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
import DatatablesJs from "../crosier/DatatablesJs";


Numeral.locale('pt-br');

let listId = "#relEstoque01List_reposicao";

function getDatatablesColumns() {
    return [
        {
            name: 'e.codProduto',
            data: 'e',
            title: 'Produto',
            render: function (data, type, row) {
                let r = '<b>' + data.codProduto + ' - ' + data.descProduto + '</b>' +
                    '<br />' +
                    '<span style="font-size: smaller">' + data.nomeFornecedor + '</span>';
                if (data.dtUltSaida) {
                    r += ' <div class="float-right"><span class="badge badge-pill badge-info">' +
                        '<i class="far fa-calendar"></i> Últ Saída: ' + Moment(data.dtUltSaida).format('DD/MM/YYYY') + '</span></div>';
                }
                return r;
            }
        },
        {
            name: 'e.qtdeMinima',
            data: 'e.qtdeMinima',
            title: 'Qtde Mín',
            render: function (data, type, row) {
                // let val = parseFloat(data.valorTotal);
                // return Numeral(val).format('0.0,[00]');
                return data;
            },
            className: 'text-center'
        },
        {
            name: 'e.qtdeAtual',
            data: 'e.qtdeAtual',
            title: 'Qtde Atual',
            render: function (data, type, row) {
                // let val = parseFloat(data.valorTotal);
                // return Numeral(val).format('0.0,[00]');
                return data;
            },
            className: 'text-center'
        },
        {
            name: 'e.custoMedio',
            data: 'e.custoMedio',
            title: 'Cto Médio',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('$ 0.0,[00]');
            },
            className: 'text-right'
        },
        {
            name: 'e.totalCustoMedio',
            data: 'e.totalCustoMedio',
            title: 'Tot Cto Médio',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return '<b>' + Numeral(val).format('$ 0.0,[00]') + '</b>';
            },
            className: 'text-right'
        },
        {
            name: 'e.precoVenda',
            data: 'e.precoVenda',
            title: 'Preço Venda',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('$ 0.0,[00]');
            },
            className: 'text-right'
        },
        {
            name: 'e.totalPrecoVenda',
            data: 'e.totalPrecoVenda',
            title: 'Tot Preço Venda',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return '<b>' + Numeral(val).format('$ 0.0,[00]') + '</b>';
            },
            className: 'text-right'
        },

    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

