'use strict';

import Moment from 'moment';

import DatatablesJs from "../crosier/DatatablesJs";

let listId = "#pv_list";

function getDatatablesColumns() {
    return [
        {
            name: 'e.codigo',
            data: 'e.codigo',
            title: 'Código'
        },
        {
            name: 'e.clienteNome',
            data: 'e',
            title: 'Cliente',
            render: function (data, type, row) {
                return data.clienteNome
            }
        },
        {
            name: 'e.id',
            data: 'e',
            title: '',
            render: function (data, type, row) {
                let colHtml = "";
                if ($(listId).data('routeedit')) {
                    let routeedit = Routing.generate($(listId).data('routeedit'), {id: data.id});
                    colHtml += DatatablesJs.makeEditButton(routeedit);
                }
                if ($(listId).data('routedelete')) {
                    let deleteUrl = Routing.generate($(listId).data('routedelete'), {id: data.id});
                    let csrfTokenDelete = $(listId).data('crsf-token-delete');
                    colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
                }
                colHtml += '<br /><span class="badge badge-pill badge-info">' + Moment(data.updated).format('DD/MM/YYYY HH:mm:ss') + '</span> ';
                return colHtml;
            },
            className: 'text-right'
        }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

