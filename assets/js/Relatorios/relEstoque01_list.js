'use strict';

import Moment from 'moment';


import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
import DatatablesJs from "../crosier/DatatablesJs";

import toastrr from "toastr";

Numeral.locale('pt-br');

let listId = "#relEstoque01List";

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e',
            title: '#',
            render: function (data, type, row) {
                return '<a role="button" href="#" ' +
                    'data-target="#confirmationModal" data-toggle="modal" data-jsfunction="Carrinho.adicionar" data-jsfunction-args="' + data.codProduto + '|' + data.descFilial + '" ' +
                    'class="btn btn-outline-primary btn-sm text-nowrap" ' +
                    'title="Adicionar ao carrinho">' +
                    '<i class="fas fa-truck"></i> Adicionar</a> ';
            },
            className: 'text-center'
        },
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
        }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());


class Carrinho {

    static adicionar(args) {
        args = args.split('|');

        $.getJSON(
            Routing.generate('relEstoque01_carrinho_adicionar', {'codProduto': args[0], 'filial': args[1]}),
            function (results) {
                if (results.produto) {
                    toastrr.success(results.msg);
                } else {
                    toastrr.error(results.msg);
                }
            }
        );
    }

    static remover() {

    }
}

global.Carrinho = Carrinho;