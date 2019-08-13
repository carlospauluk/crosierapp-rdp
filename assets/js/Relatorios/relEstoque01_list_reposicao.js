'use strict';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Moment from 'moment';


import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
import DatatablesJs from "../crosier/DatatablesJs";
import $ from "jquery";
import toastrr from "toastr";

Routing.setRoutingData(routes);

Numeral.locale('pt-br');

let listId = "#relEstoque01List_reposicao";

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
                let val = parseFloat(data);
                return Numeral(val).format('0.0,[00]');
            },
            className: 'text-center'
        },
        {
            name: 'e.qtdeAtual',
            data: 'e.qtdeAtual',
            title: 'Qtde Atual',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('0.0,[00]');
            },
            className: 'text-center'
        },
        {
            name: 'e.deficit',
            data: 'e.deficit',
            title: 'Qtde Reposição',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('0.0,[00]');
            },
            className: 'text-center'
        },
        {
            name: 'e.deficit',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";

                if (data.temCompras) {
                    let url = Routing.generate('relCompras01_listComprasPorProduto', {'filter': {'codProduto': data.codProduto}});
                    colHtml += '<a role="button" class="btn btn-outline-primary btn-sm text-nowrap" href="' + url + '">' +
                        '<i class="fas fa-truck"></i> Compras</a> ';
                }

                return colHtml;
            },
            className: 'text-right'
        }


    ];
}


$(document).ready(function () {

    DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

    let $selTodasMovs = $('#selTodasMovs');
    let $btnImprimirListaReposicao = $('#btnImprimirListaReposicao');


    $btnImprimirListaReposicao.click(function () {
        let url = $(this).data('url');
        let form = $('<form>').attr("method", "post").attr(
            "action", url);
        form.append($('.movSel').clone());
        // simuland o clique
        form.append('<input type="hidden" name="btnImprimirListaReposicao" value="1" />');
        $(form).appendTo('body').submit();

    });


});


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