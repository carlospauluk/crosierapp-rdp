'use strict';

import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';
import DatatablesJs from "../crosier/DatatablesJs";

import toastrr from "toastr";

Numeral.locale('pt-br');

let listId = "#ven_pv_relEstoque01List";

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
                    'data-target="#confirmationModal" data-toggle="modal" data-jsfunction="PV.adicionar" data-jsfunction-args="' + data.codProduto + '|' + data.codFornecedor + '|' + data.descFilial + '|' + data.id + '" ' +
                    'class="btn btn-outline-primary text-nowrap ml-2" ' +
                    'title="Adicionar ao PV">' +
                    '<i class="fas fa-truck"></i> Adicionar</a> ';

                return html;
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
            name: 'e.precoVenda',
            data: 'e.precoVenda',
            title: 'Preço Venda',
            render: function (data, type, row) {
                let val = parseFloat(data.precoVenda);
                return Numeral(val).format('0.0,[00]');
            },
            className: 'text-center'
        }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());


class PV {

    static adicionar(args) {
        args = args.split('|');


        let pvId = $('#span_pvId').html();
        let $campoQtde = $('#qtde_' + args[3]);
        let qtde = $campoQtde.val();

        let params = {
            'pv': pvId,
            'codProduto': args[0],
            'codFornecedor': args[1],
            'filial': args[2],
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