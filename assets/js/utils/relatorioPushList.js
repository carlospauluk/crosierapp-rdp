'use strict';



import DatatablesJs from "../crosier/DatatablesJs";
import Moment from 'moment';

let listId = "#relatorioPushList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Id'
        },
        {
            name: 'e.descricao',
            data: 'e',
            title: 'Arquivo',
            render: function (data, type, row) {
                return '<a href="/uploads/relatoriospush/' + data.arquivo + '" target="_blank">' + data.descricao + '</a>';
            },
        },
        {
            name: 'e.dtEnvio',
            data: 'e.dtEnvio',
            title: 'Dt Envio',
            render: function (data, type, row) {
                return Moment(data).format('DD/MM/YYYY HH:mm:ss');
            },
            className: 'text-center'

        },
        // {
        //     name: 'e.id',
        //     data: 'e',
        //     title: '',
        //     render: function (data, type, row) {
        //         let colHtml = "";
        //         if ($(listId).data('routeedit')) {
        //             let routeedit = Routing.generate($(listId).data('routeedit'), {id: data.id});
        //             colHtml += DatatablesJs.makeEditButton(routeedit);
        //         }
        //         if ($(listId).data('routedelete')) {
        //             let deleteUrl = Routing.generate($(listId).data('routedelete'), {id: data.id});
        //             let csrfTokenDelete = $(listId).data('crsf-token-delete');
        //             colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
        //         }
        //         return colHtml;
        //     },
        //     className: 'text-right'
        // }
    ];
}


DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());

