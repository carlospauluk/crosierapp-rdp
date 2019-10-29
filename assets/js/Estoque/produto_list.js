'use strict';

import Moment from 'moment';

import DatatablesJs from "../crosier/DatatablesJs";

import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';

import $ from "jquery";

Numeral.locale('pt-br');

let listId = "#produto_list";
let crosierAppVendestUrl = $('#listAuxDatas').data('json').crosierAppVendestUrl;

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
                let s = '<div class="float-left">' + data.titulo + '</div>';
                if (data.imagem1) {
                    s += '<div class="float-right"><img src="' + crosierAppVendestUrl + '/' + data.codigoDepto + '/' + data.codigoGrupo + '/' + data.codigoSubgrupo + '/' + data.imagem1 + '" width="50px"/></div>';
                }
                return s;
            },
        },
        {
            name: 'e.nomeDepto',
            data: 'e.nomeDepto',
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
            data: 'e',
            title: 'Status Cad',
            render: function (data, type, row) {
                return Numeral(parseFloat(data.porcentPreench) * 100).format('0,0') + '%';
            }
        },
        {
            name: 'e.saldoEstoqueMatriz',
            data: 'e.saldoEstoqueMatriz',
            title: 'Estoque Matriz',
            render: function (data, type, row) {
                return Numeral(parseFloat(data.estoqueMatriz)).format('0,0[.000]');
            }
        },
        {
            name: 'e.updated',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";
                let routeedit = crosierAppVendestUrl + '/est/produto/form/' + data.id;
                colHtml += DatatablesJs.makeEditButton(routeedit);
                colHtml += '<br /><span class="badge badge-pill badge-info">' + Moment(data.updated).format('DD/MM/YYYY HH:mm:ss') + '</span> ';
                return colHtml;
            },
            className: 'text-right'
        }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

