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
                return data.codProduto + ' - ' + data.descProduto;
            }
        },
        {
            name: 'e.nomeFornecedor',
            data: 'e.nomeFornecedor',
            title: 'Fornecedor',
            render: function (data, type, row) {
                return data;
            }
        },
        {
            name: 'e.dtUltSaida',
            data: 'e.dtUltSaida',
            title: 'Dt Últ Saída',
            render: function (data, type, row) {
                return data ? Moment(data).format('DD/MM/YYYY') : 'N/A';
            },
            className: 'text-center'

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
            name: 'e.qtdeAtual',
            data: 'e.deficit',
            title: 'Déficit',
            render: function (data, type, row) {
                // let val = parseFloat(data.valorTotal);
                // return Numeral(val).format('0.0,[00]');
                return data;
            },
            className: 'text-center'
        }


    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

