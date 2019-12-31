'use strict';

import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
;

import toastrr from "toastr";
import $ from "jquery";

Numeral.locale('pt-br');

let listId = "#ven_pv_produtoList";

let crosierAppVendestUrl = $('#listAuxDatas').data('json').crosierAppVendestUrl;

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e',
            title: '#',
            render: function (data, type, row) {
                let html = '<div class="d-sm-flex flex-nowrap ml-auto align-items-center">';

                html += '<input type="number" style="width: 80px" id="qtde_' + data.id + '" class="form-control">';

                html += '<a role="button" href="#" ' +
                    'data-target="#confirmationModal" data-toggle="modal" data-jsfunction="PV.adicionar" data-jsfunction-args="' + data.id + '" ' +
                    'class="btn btn-outline-primary text-nowrap ml-2" ' +
                    'title="Adicionar ao PV">' +
                    '<i class="fas fa-truck"></i> Adicionar</a> ';

                return html;
            },
            className: 'text-center'
        },
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Código'
        },
        {
            name: 'e.id',
            data: 'e',
            title: 'Produto',
            render: function (data, type, row) {

                let s = '<div class="float-left">' + (data.titulo ? data.titulo : '<span style="font-size: small; font-style: italic; color: grey">' + data.nome + '</span>') + '</div>';
                if (data.imagem1) {
                    s += '<div class="float-right"><img src="' + crosierAppVendestUrl + '/images/produtos/' + data.deptoId + '/' + data.grupoId + '/' + data.subgrupoId + '/' + data.imagem1 + '" width="50px"/></div>';
                }
                return s;
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
            name: 'e.saldoEstoqueTotal',
            data: 'e.saldoEstoqueTotal',
            title: 'Saldo Total',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('0.0,[000]');
            },
            className: 'text-center'
        },
        {
            name: 'e.precoTabela',
            data: 'e.precoTabela',
            title: 'Preço Tabela',
            render: function (data, type, row) {
                let val = parseFloat(data);
                return Numeral(val).format('0.0,[00]');
            },
            className: 'text-center'
        }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());


class PV {

    static adicionar(produtoId) {


        let pvId = $('#span_pvId').html();
        let $campoQtde = $('#qtde_' + produtoId);
        let qtde = $campoQtde.val();

        let params = {
            'pv': pvId,
            'produtoId': produtoId,
            'qtde': qtde
        };

        console.dir(params);

        $.getJSON(
            Routing.generate('ven_pv_adicionarItem', params),
            function (results) {
                if (results.produto) {
                    $campoQtde.val('');
                    toastrr.success(results.msg);
                } else {
                    toastrr.error(results.msg);
                }
            }
        )
        ;
    }

}

global.PV = PV;