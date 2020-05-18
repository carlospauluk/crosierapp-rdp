'use strict';

import $ from "jquery";

import Moment from 'moment';
import Numeral from 'numeral';

import 'numeral/locales/pt-br.js';

Numeral.locale('pt-br');

let listId = "#produto_list";
let crosierappradx_url = $('#listAuxDatas').data('json').crosierappradx_url;

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Código'
        },
        {
            name: 'e.titulo',
            data: 'e',
            title: 'Título',
            render: function (data, type, row) {
                let s = '<div class="float-left">' +
                    (data.jsonData && data.jsonData.titulo ? data.jsonData.titulo : '<span style="font-size: small; font-style: italic; color: grey">' + data.nome + '</span>') +
                    '</div>';
                if (data.jsonData && data.jsonData.imagem1) {
                    s += '<div class="float-right">' +
                        '<img src="' + crosierappradx_url + '/images/produtos/' + data.depto.id + '/' + data.grupo.id + '/' + data.subgrupo.id + '/' + data.jsonData.imagem1 + '" width="50px"/></div>';
                }
                return s;
            },
        },
        {
            name: 'e.deptoNome',
            data: 'e.deptoNome',
            title: 'Depto',
            render: function (data, type, row) {
                return data;
            }
        },
        {
            name: 'e.status',
            data: 'e.status',
            title: 'Status',
            render: function (data, type, row) {
                return data;
            }
        },
        {
            name: 'e.porcentPreench',
            data: 'e.porcentPreench',
            title: 'Status Cad',
            render: function (data, type, row) {
                return Numeral(parseFloat(data) * 100).format('0,0') + '%';
            }
        },
        {
            name: 'e.qtdeImagens',
            data: 'e.qtdeImagens',
            title: 'Qtde Imagens',
            className: 'text-center'
        },
        {
            name: 'e.qtdeEstoqueTotal',
            data: 'e.qtdeEstoqueTotal',
            title: 'Estoque Total',
            render: function (data, type, row) {
                return Numeral(parseFloat(data)).format('0,0[000]');
            },
            className: 'text-right'
        },
        {
            name: 'e.updated',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";
                let routeedit = crosierappradx_url + '/est/produto/form/' + data.id;
                colHtml += DatatablesJs.makeEditButton(routeedit);
                colHtml += '<br /><span class="badge badge-pill badge-info">' + Moment(data.updated).format('DD/MM/YYYY HH:mm:ss') + '</span> ';
                return colHtml;
            },
            className: 'text-right'
        }
    ];
}

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

